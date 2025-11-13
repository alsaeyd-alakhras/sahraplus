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

    this.viewedIds = new Set();
    this.currentPage = 1;
    this.isLoadingMore = false;
    this.hasMore = true;
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

      if (this.hasMore && !this.isLoadingMore && this.currentIndex >= this.videoItems.length - 2) {
        this.loadMoreShorts();
      }
    });
  }

  setupVideoObserver() {
    this.videoObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          const video = entry.target;
          const videoItem = video.closest(".video-item");
          const videoId = parseInt(videoItem.dataset.videoId);

          if (entry.isIntersecting && entry.intersectionRatio > 0.7) {
            this.currentIndex = videoId - 1;
            this.ensureVideoSrc(video);
            this.playVideo(video);
            this.trackView(video, videoId);
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
      this.videoObserver.observe(video);
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

  ensureVideoSrc(video) {
    if (!video.getAttribute("src")) {
      const ds = video.getAttribute("data-src");
      if (ds) {
        video.setAttribute("src", ds);
      }
    }
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

  trackView(video, videoId) {
    if (this.viewedIds.has(videoId)) return;
    const onTimeUpdate = () => {
      try {
        const duration = video.duration || 0;
        const thresholdSec = Math.min(6, Math.max(3, Math.floor(duration * 0.1) || 5));
        if (video.currentTime >= thresholdSec) {
          this.viewedIds.add(videoId);
          video.removeEventListener("timeupdate", onTimeUpdate);
          fetch(`/api/v1/shorts/${videoId}/view`, {
            method: "POST",
            headers: {
              Accept: "application/json",
              "Content-Type": "application/json",
            },
            credentials: "include",
            body: JSON.stringify({
              watch_duration: Math.floor(video.currentTime),
              completion_percentage: duration > 0 ? Math.round((video.currentTime / duration) * 100) : 0,
            }),
          }).catch(() => {});
        }
      } catch (_) {}
    };
    video.addEventListener("timeupdate", onTimeUpdate);
  }

  async loadMoreShorts() {
    this.isLoadingMore = true;
    try {
      const nextPage = this.currentPage + 1;
      const res = await fetch(`/api/v1/shorts?page=${nextPage}`, {
        headers: { Accept: "application/json" },
        credentials: "include",
      });
      if (!res.ok) throw new Error("Failed to load more shorts");
      const json = await res.json();
      const items = Array.isArray(json.data) ? json.data : [];
      if (items.length === 0) {
        this.hasMore = false;
        return;
      }
      const frag = document.createDocumentFragment();
      items.forEach((item) => {
        const el = this.createShortItemElement(item);
        frag.appendChild(el);
      });
      this.container.appendChild(frag);
      this.videoItems = document.querySelectorAll(".video-item");
      items.forEach((_, idx) => {
        const itemEl = this.videoItems[this.videoItems.length - items.length + idx];
        const video = itemEl.querySelector("video");
        const wrapper = itemEl.querySelector(".video-wrapper");
        if (video.readyState >= 1) {
          this.setVideoSize(video, wrapper);
        } else {
          video.addEventListener("loadedmetadata", () => this.setVideoSize(video, wrapper));
        }
        this.videoObserver.observe(video);
      });
      this.currentPage = nextPage;
      this.updateNavigationVisibility();
    } catch (e) {
      console.error(e);
      this.hasMore = false;
    } finally {
      this.isLoadingMore = false;
    }
  }

  createShortItemElement(item) {
    const wrapperDiv = document.createElement("div");
    wrapperDiv.className = "flex justify-center items-center h-screen video-item";
    wrapperDiv.setAttribute("data-video-id", item.id);
    const aspect = item.aspect_ratio || "vertical";
    const poster = item.poster_full_path || item.poster || "";
    const videoUrl = item.video_full_url || item.video_url || "";
    const title = item.title || "";
    const description = item.description || "";
    const likes = item.likes_count ?? 0;
    const comments = item.comments_count ?? 0;
    const shares = item.shares_count ?? 0;
    const shareUrl = item.share_url || videoUrl;

    wrapperDiv.innerHTML = `
      <div class="relative bg-gray-800 rounded-lg shadow-xl backdrop-blur-md backdrop-brightness-75 shadow-white/40 video-wrapper group" data-aspect="${aspect}">
        <video class="object-contain w-full h-full rounded-lg cursor-pointer" loop muted playsinline
          poster="${poster}" data-src="${videoUrl}" data-video-url="${videoUrl}"></video>

        <div class="flex absolute inset-0 z-30 justify-center items-center opacity-0 transition-opacity duration-300 pointer-events-none play-pause-overlay">
          <i class="text-6xl text-white fas fa-play"></i>
        </div>

        <div class="absolute right-0 bottom-0 left-0 z-20 px-3 py-2 bg-gradient-to-t to-transparent opacity-0 transition duration-300 video-controls from-black/70 group-hover:opacity-100">
          <div class="flex justify-between items-center mt-2 text-lg text-white">
            <div class="relative w-full h-2 bg-gray-600 rounded-full cursor-pointer">
              <div class="absolute top-0 left-0 w-0 h-full bg-red-500 rounded-full progress-bar"></div>
            </div>
            <button class="px-4 volume-btn"><i class="fas fa-volume-up"></i></button>
            <button class="fullscreen-btn"><i class="fas fa-expand"></i></button>
          </div>
        </div>

        <div class="flex absolute left-3 bottom-4 z-10 flex-col items-center transition-all duration-200 group-hover:bottom-20">
          <a href="${shareUrl}" class="flex flex-col justify-center items-center w-12 h-12 rounded-full transition-all duration-200 action-btn" data-video="${item.id}">
            <i class="text-xl fas fa-play"></i>
          </a>
          <div class="text-xs">مشاهدة</div>
          <button class="flex flex-col justify-center items-center w-12 h-12 rounded-full transition-all duration-200 like-btn action-btn" data-video="${item.id}">
            <i class="text-xl fas fa-heart"></i>
          </button>
          <div class="mb-2 text-xs like-count like-btn-${item.id}">${likes}</div>

          <button class="action-btn comment-btn" data-video="${item.id}">
            <i class="text-xl fas fa-comment"></i>
          </button>
          <div class="mb-2 text-xs comment-count comment-btn-${item.id}">${comments}</div>

          <button class="action-btn share-btn" data-video="${item.id}" data-url="${shareUrl}">
            <i class="text-xl fas fa-share"></i>
          </button>
          <div class="mb-2 text-xs share-count share-btn-${item.id}">${shares}</div>

          <button class="action-btn save-btn" data-video="${item.id}">
            <i class="text-xl fas fa-bookmark"></i>
          </button>
          <div class="text-xs">حفظ</div>
        </div>

        <div class="flex absolute -right-14 top-1/2 z-10 flex-col items-center transform -translate-y-1/2 navigation-arrows">
          <button class="hidden nav-arrow prev-btn" data-direction="up">
            <i class="text-xl fas fa-chevron-up"></i>
          </button>
          <button class="nav-arrow next-btn" data-direction="down">
            <i class="text-xl fas fa-chevron-down"></i>
          </button>
        </div>

        <div class="absolute right-2 left-[5rem] bottom-4 text-white z-10 group-hover:bottom-24 transition-all duration-200">
          <h3 class="text-lg font-bold">${title}</h3>
          <p class="text-sm opacity-80 line-clamp-2">${description}</p>
        </div>
      </div>
    `;

    return wrapperDiv;
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

  // Like button functionality with API
  $(document).on("click", ".like-btn", async function () {
    const $btn = $(this);
    const heart = $btn.find("i");
    const videoId = $btn.data("video");
    const countEl = $(".like-count.like-btn-" + videoId);

    let countText = countEl.text();
    let count = 0;

    if (countText.includes("K")) {
      count = parseFloat(countText.replace("K", "")) * 1000;
    } else {
      count = parseInt(countText) || 0;
    }

    const wasLiked = $btn.hasClass("liked");
    
    // Optimistic UI update
    if (wasLiked) {
      $btn.removeClass("liked text-red-500");
      count -= 1;
    } else {
      $btn.addClass("liked text-red-500");
      count += 1;
    }

    // Update count display
    if (count >= 1000) {
      countEl.text((count / 1000).toFixed(1) + "K");
    } else {
      countEl.text(count);
    }

    // Call API
    try {
      const response = await fetch(`/api/v1/shorts/${videoId}/like`, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        credentials: 'include'
      });
      
      if (!response.ok) {
        throw new Error('Failed to like');
      }
      
      const data = await response.json();
      // Update with server count
      if (data.likes_count >= 1000) {
        countEl.text((data.likes_count / 1000).toFixed(1) + "K");
      } else {
        countEl.text(data.likes_count);
      }
    } catch (error) {
      console.error('Like error:', error);
      // Revert on failure
      if (wasLiked) {
        $btn.addClass("liked text-red-500");
      } else {
        $btn.removeClass("liked text-red-500");
      }
      count = wasLiked ? count + 1 : count - 1;
      if (count >= 1000) {
        countEl.text((count / 1000).toFixed(1) + "K");
      } else {
        countEl.text(count);
      }
    }
  });

  // Save button functionality with API
  $(document).on("click", ".save-btn", async function () {
    const $btn = $(this);
    const videoId = $btn.data("video");
    const $icon = $btn.find("i");
    
    const wasSaved = $btn.hasClass("saved");
    
    // Optimistic UI update
    if (wasSaved) {
      $btn.removeClass("saved text-yellow-500");
      $icon.removeClass("fas").addClass("far");
    } else {
      $btn.addClass("saved text-yellow-500");
      $icon.removeClass("far").addClass("fas");
    }

    // Call API
    try {
      const response = await fetch(`/api/v1/shorts/${videoId}/save`, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        credentials: 'include'
      });
      
      if (!response.ok) {
        throw new Error('Failed to save');
      }
    } catch (error) {
      console.error('Save error:', error);
      // Revert on failure
      if (wasSaved) {
        $btn.addClass("saved text-yellow-500");
        $icon.removeClass("far").addClass("fas");
      } else {
        $btn.removeClass("saved text-yellow-500");
        $icon.removeClass("fas").addClass("far");
      }
    }
  });

  // Share button functionality with API
  $(document).on("click", ".share-btn", async function () {
    const videoId = $(this).data("video");
    const videoUrl = $(this).data("url") || $(this)
      .closest(".video-item")
      .find("video")
      .data("video-url");
    
    // Try Web Share API first
    if (navigator.share) {
      try {
        await navigator.share({
          title: 'Check out this short video!',
          url: videoUrl
        });
        
        // Increment share count on server
        await fetch(`/api/v1/shorts/${videoId}/share`, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          credentials: 'include'
        });
        
        // Update count
        const countEl = $(".share-count.share-btn-" + videoId);
        let count = parseInt(countEl.text()) || 0;
        count += 1;
        if (count >= 1000) {
          countEl.text((count / 1000).toFixed(1) + "K");
        } else {
          countEl.text(count);
        }
      } catch (err) {
        if (err.name !== 'AbortError') {
          console.log('Share failed:', err);
        }
      }
    } else {
      // Fallback to modal
      $("#share-url").val(videoUrl);
      $(".share-popup").addClass("open");
      $(".share-overlay").removeClass("hidden");
      $("body").addClass("overflow-hidden");
    }
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
  $(".social-share-btn").click(async function () {
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
      
      // Increment share count on server if we have currentVideoId
      if (currentVideoId) {
        try {
          await fetch(`/api/v1/shorts/${currentVideoId}/share`, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            credentials: 'include'
          });
        } catch (err) {
          console.log('Share count update failed:', err);
        }
      }
    }
  });

  // Comment button functionality
  $(document).on("click", ".comment-btn", function () {
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
