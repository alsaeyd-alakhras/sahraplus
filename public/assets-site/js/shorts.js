import Lenis from "https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.42/+esm";

class ShortsVideoPlayer {
  constructor() {
    this.container = document.querySelector("#shorts-container");
    this.videoItems = document.querySelectorAll(".video-item");
    this.currentIndex = 0;
    this.hasInteracted = false;
    this.isScrolling = false;
    this.scrollTimeout = null;

    this.init();
  }

  init() {
    this.setupLenis();
    this.setupVideoObserver();
    this.setupNavigationButtons();
    this.adjustVideoSizes();
    this.updateNavigationVisibility();
  }

  setupLenis() {
    this.lenis = new Lenis({
      wrapper: this.container,
      content: this.container,
      duration: 1.2,
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      direction: "vertical",
      gestureDirection: "vertical",
      smooth: true,
      smoothTouch: false,
      touchMultiplier: 2,
    });

    const raf = (time) => {
      this.lenis.raf(time);
      requestAnimationFrame(raf);
    };
    requestAnimationFrame(raf);

    this.lenis.on("scroll", () => {
      this.isScrolling = true;
      clearTimeout(this.scrollTimeout);
      this.scrollTimeout = setTimeout(() => {
        this.isScrolling = false;
        this.handleScrollEnd();
      }, 150);
    });
  }

  setupVideoObserver() {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          const video = entry.target;
          const videoItem = video.closest(".video-item");
          const videoId = parseInt(videoItem.dataset.videoId);

          if (entry.isIntersecting && entry.intersectionRatio > 0.7) {
            this.currentIndex = videoId - 1;
            this.playVideo(video);
            this.updateNavigationVisibility();
          } else {
            this.pauseVideo(video);
          }
        });
      },
      {
        threshold: [0.7],
        root: this.container,
      }
    );

    this.videoItems.forEach((item) => {
      const video = item.querySelector("video");
      observer.observe(video);
    });
  }

  setupNavigationButtons() {
    document.addEventListener("click", (e) => {
      if (e.target.closest(".nav-arrow")) {
        const direction = e.target.closest(".nav-arrow").dataset.direction;
        this.navigateVideo(direction);
      }
    });
  }
  adjustVideoSizes() {
    this.videoItems.forEach((item) => {
      const video = item.querySelector("video");
      const wrapper = item.querySelector(".video-wrapper");

      video.addEventListener("loadedmetadata", () => {
        this.setVideoSize(video, wrapper);
      });

      // إذا كان الفيديو محمل بالفعل
      if (video.readyState >= 1) {
        this.setVideoSize(video, wrapper);
      }
    });
  }

  setVideoSize(video, wrapper) {
    const aspectRatio = video.videoWidth / video.videoHeight;

    // إزالة الكلاسات القديمة
    wrapper.classList.remove(
      "w-[75vw]",
      "h-[70vh]",
      "w-[340px]",
      "md:w-[400px]",
      "max-h-[90vh]"
    );

    if (aspectRatio > 1.2) {
      // فيديو عرضي
      wrapper.classList.add("w-[75vw]", "max-h-[70vh]");
      wrapper.dataset.aspect = "horizontal";
    } else {
      // فيديو طولي أو مربع
      wrapper.classList.add("w-[340px]", "md:w-[400px]", "max-h-[90vh]");
      wrapper.dataset.aspect = "vertical";
    }
  }

  navigateVideo(direction) {
    const targetIndex =
      direction === "down" ? this.currentIndex + 1 : this.currentIndex - 1;

    if (targetIndex >= 0 && targetIndex < this.videoItems.length) {
      const targetPosition = targetIndex * window.innerHeight;
      this.lenis.scrollTo(targetPosition, {
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      });
    }
  }

  updateNavigationVisibility() {
    this.videoItems.forEach((item, index) => {
      const prevBtn = item.querySelector(".prev-btn");
      const nextBtn = item.querySelector(".next-btn");

      // إخفاء زر الصعود في الفيديو الأول
      if (index === 0) {
        prevBtn?.classList.add("hidden");
      } else {
        prevBtn?.classList.remove("hidden");
      }

      // إخفاء زر الهبوط في الفيديو الأخير
      if (index === this.videoItems.length - 1) {
        nextBtn?.classList.add("hidden");
      } else {
        nextBtn?.classList.remove("hidden");
      }
    });
  }

  handleScrollEnd() {
    const scrollPosition = this.container.scrollTop;
    const newIndex = Math.round(scrollPosition / window.innerHeight);

    if (
      newIndex !== this.currentIndex &&
      newIndex >= 0 &&
      newIndex < this.videoItems.length
    ) {
      this.currentIndex = newIndex;
      this.updateNavigationVisibility();
    }
  }

  playVideo(video) {
    if (!this.hasInteracted) {
      video.muted = false;
      // أول تفاعل - تفعيل الصوت
      document.addEventListener(
        "touchstart",
        () => {
          this.hasInteracted = true;
          video.muted = false;
        },
        { once: true }
      );

      document.addEventListener(
        "click",
        () => {
          this.hasInteracted = true;
          video.muted = false;
        },
        { once: true }
      );
    } else {
      video.muted = false;
    }

    video.play().catch(console.log);
  }

  pauseVideo(video) {
    video.pause();
  }
}

// تشغيل التطبيق عند تحميل الصفحة
document.addEventListener("DOMContentLoaded", () => {
  new ShortsVideoPlayer();
});

// التعامل مع تغيير حجم الشاشة
window.addEventListener("resize", () => {
  // إعادة حساب أحجام الفيديو عند تغيير حجم الشاشة
  setTimeout(() => {
    const player = new ShortsVideoPlayer();
    player.adjustVideoSizes();
  }, 100);
});

$(document).ready(function () {
  // Sample comments data
  const commentsData = {
    1: [
      {
        id: 1,
        username: "tarekouti5744",
        avatar: "T",
        text: "اثا شخصيا احب يك اليوم انخرط في الجبل النقي هيم لنا",
        likes: 12,
        liked: false,
        replies: [
          {
            id: 11,
            username: "user123",
            avatar: "U",
            text: "تماماً أوافقك الرأي",
            likes: 3,
            liked: false,
          },
        ],
      },
      {
        id: 2,
        username: "lam_pro76",
        avatar: "L",
        text: "صرت مشهور اليوم أبصر أيضاً في عز",
        likes: 9,
        liked: false,
        replies: [],
      },
    ],
    2: [
      {
        id: 3,
        username: "sandy.al.hasan",
        avatar: "S",
        text: "كيف تصبحين حوائي في عشر ثواني",
        likes: 2981,
        liked: false,
        replies: [],
      },
    ],
    3: [
      {
        id: 4,
        username: "nawafel",
        avatar: "N",
        text: "لو بقوا فلسطين براهيم اسهيد و ايت 2022",
        likes: 427,
        liked: false,
        replies: [],
      },
    ],
  };

  let currentVideoId = null;
  let replyingTo = null;

  // Like button functionality
  $(".like-btn").click(function () {
    const heart = $(this).find("i");
    const videoId = $(this).data("video");
    const countEl = $(".like-count.like-btn-" + videoId);

    let countText = countEl.text();
    let count = 0;

    if (countText.includes("K")) {
      count = parseFloat(countText.replace("K", "")) * 1000;
    } else {
      count = parseInt(countText);
    }

    if ($(this).hasClass("liked")) {
      // إلغاء الإعجاب
      $(this).removeClass("liked text-red-500");
      count -= 1;
    } else {
      // إضافة إعجاب
      $(this).addClass("liked text-red-500");
      count += 1;
    }

    // تحديث العدد
    if (count >= 1000) {
      countEl.text((count / 1000).toFixed(1) + "K");
    } else {
      countEl.text(count);
    }
  });

  // Share button functionality
  $(".share-btn").click(function () {
    const videoId = $(this).data("video");
    const videoUrl = $(this)
      .closest(".video-item")
      .find("video")
      .data("video-url");
    $("#share-url").val(videoUrl);
    $(".share-popup").addClass("open");
    $(".share-overlay").removeClass("hidden");
    $("body").addClass("overflow-hidden");
  });

  // Close share popup
  $(".share-overlay").click(function (e) {
    if (e.target === this || $(e.target).closest(".close-share").length > 0) {
      $(".share-popup").removeClass("open");
      $(".share-overlay").addClass("hidden");
      $("body").removeClass("overflow-hidden");
    }
  });

  // Copy link functionality
  $("#copy-link").click(function () {
    const url = $("#share-url").val();
    navigator.clipboard.writeText(url).then(function () {
      const $btn = $("#copy-link");
      const originalHtml = $btn.html();
      $btn
        .html('<span class="text-xs">Copied!</span>')
        .addClass("bg-green-600");
      setTimeout(function () {
        $btn.html(originalHtml).removeClass("bg-green-600");
      }, 2000);
    });
  });

  // Social share buttons
  $(".social-share-btn").click(function () {
    const platform = $(this).data("platform");
    const url = encodeURIComponent($("#share-url").val());
    let shareUrl = "";

    switch (platform) {
      case "whatsapp":
        shareUrl = `https://wa.me/?text=${url}`;
        break;
      case "facebook":
        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
        break;
      case "twitter":
        shareUrl = `https://twitter.com/intent/tweet?url=${url}`;
        break;
      case "telegram":
        shareUrl = `https://t.me/share/url?url=${url}`;
        break;
    }

    if (shareUrl) {
      window.open(shareUrl, "_blank", "width=600,height=400");
    }
  });

  // Comment button functionality
  $(".comment-btn").click(function () {
    currentVideoId = $(this).data("video");
    loadComments(currentVideoId);
    $(".comment-panel").addClass("open");
    $(".comment-overlay").removeClass("hidden");
    $("body").addClass("overflow-hidden");
  });

  // Close comments
  $(".close-comments, .comment-overlay").click(function () {
    $(".comment-panel").removeClass("open");
    $(".comment-overlay").addClass("hidden");
    $("body").removeClass("overflow-hidden");
    replyingTo = null;
    $("#comment-input").attr("placeholder", "Add a comment...").val("");
  });

  // Load comments for a video
  function loadComments(videoId) {
    const comments = commentsData[videoId] || [];
    const $container = $(".comments-container");
    $container.empty();

    comments.forEach((comment) => {
      const commentHtml = createCommentHtml(comment);
      $container.append(commentHtml);
    });
  }

  // Create comment HTML
  function createCommentHtml(comment, isReply = false) {
    const replyClass = isReply ? "reply-indent" : "";
    return `
            <div class="comment-item ${replyClass}" data-comment-id="${comment.id}">
                <div class="flex items-start space-x-3">
                    <div class="flex flex-shrink-0 justify-center items-center w-8 h-8 text-sm font-bold bg-gradient-to-r from-blue-500 to-purple-500 rounded-full">
                        ${comment.avatar}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold">${
                              comment.username
                            }</span>
                            <span class="text-xs text-gray-400">1 year ago</span>
                        </div>
                        <p class="mt-1 text-sm break-words">${comment.text}</p>
                        <div class="flex items-center mt-2 space-x-4">
                            <button class="flex items-center space-x-1 text-xs text-gray-400 comment-like-btn hover:text-white" data-comment-id="${
                              comment.id
                            }">
                                <svg class="w-4 h-4 heart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 000-6.364 4.5 4.5 0 00-6.364 0L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span class="like-count">${comment.likes}</span>
                            </button>
                            ${
                              !isReply
                                ? `<button class="text-xs text-gray-400 reply-btn hover:text-white" data-username="${comment.username}" data-comment-id="${comment.id}">Reply</button>`
                                : ""
                            }
                        </div>
                        ${
                          comment.replies && comment.replies.length > 0
                            ? `
                            <button class="mt-2 text-xs text-blue-400 view-replies-btn hover:text-blue-300" data-comment-id="${
                              comment.id
                            }">
                                View ${comment.replies.length} replies
                            </button>
                            <div class="hidden mt-2 replies-container">
                                ${comment.replies
                                  .map((reply) =>
                                    createCommentHtml(reply, true)
                                  )
                                  .join("")}
                            </div>
                        `
                            : ""
                        }
                    </div>
                </div>
            </div>
        `;
  }

  // Handle comment interactions
  $(document).on("click", ".comment-like-btn", function () {
    const $this = $(this);
    const $heart = $this.find(".heart-icon");
    const $count = $this.find(".like-count");

    if ($heart.attr("fill") === "none") {
      $heart.attr("fill", "#ef4444").attr("stroke", "#ef4444");
      $this.addClass("text-red-500");
      $count.text(parseInt($count.text()) + 1);
    } else {
      $heart.attr("fill", "none").attr("stroke", "currentColor");
      $this.removeClass("text-red-500");
      $count.text(parseInt($count.text()) - 1);
    }
  });

  // Handle reply button
  $(document).on("click", ".reply-btn", function () {
    const username = $(this).data("username");
    const commentId = $(this).data("comment-id");
    replyingTo = commentId;
    $("#comment-input").attr("placeholder", `Reply to @${username}...`).focus();
  });

  // Handle view replies
  $(document).on("click", ".view-replies-btn", function () {
    const $this = $(this);
    const $repliesContainer = $this.siblings(".replies-container");

    if ($repliesContainer.hasClass("hidden")) {
      $repliesContainer.removeClass("hidden");
      $this.text("Hide replies");
    } else {
      $repliesContainer.addClass("hidden");
      const replyCount = $repliesContainer.children().length;
      $this.text(`View ${replyCount} replies`);
    }
  });

  // Send comment
  $("#send-comment, #comment-input").on("click keypress", function (e) {
    if (e.type === "click" || e.which === 13) {
      const commentText = $("#comment-input").val().trim();
      if (commentText) {
        const newComment = {
          id: Date.now(),
          username: "You",
          avatar: "U",
          text: commentText,
          likes: 0,
          liked: false,
          replies: [],
        };

        if (replyingTo) {
          // Add as reply
          const $parentComment = $(
            `.comment-item[data-comment-id="${replyingTo}"]`
          );
          let $repliesContainer = $parentComment.find(".replies-container");

          if ($repliesContainer.length === 0) {
            $parentComment.find(".flex-1").append(`
                            <button class="mt-2 text-xs text-blue-400 view-replies-btn hover:text-blue-300" data-comment-id="${replyingTo}">
                                View 1 replies
                            </button>
                            <div class="mt-2 replies-container">
                            </div>
                        `);
            $repliesContainer = $parentComment.find(".replies-container");
          } else {
            $repliesContainer.removeClass("hidden");
            const $viewBtn = $parentComment.find(".view-replies-btn");
            const currentCount = $repliesContainer.children().length + 1;
            $viewBtn.text(`Hide replies`);
          }

          $repliesContainer.append(createCommentHtml(newComment, true));
          replyingTo = null;
          $("#comment-input").attr("placeholder", "Add a comment...");
        } else {
          // Add as main comment
          $(".comments-container").prepend(createCommentHtml(newComment));
        }

        $("#comment-input").val("");
      }
    }
  });

  // Initialize with first video comments
  loadComments(1);
});
