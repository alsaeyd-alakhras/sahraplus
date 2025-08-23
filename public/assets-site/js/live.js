// Initialize the page
$(document).ready(function () {
  updateCurrentTime();
  renderChannels();
  updateTimeIndicators();

  // Update time every minute
  setInterval(updateCurrentTime, 60000);
  setInterval(updateTimeIndicators, 60000);

  // Event listeners
  setupEventListeners();
});

function updateCurrentTime() {
  const now = new Date();
  const timeString = now.toLocaleTimeString("ar-SA", {
    hour: "2-digit",
    minute: "2-digit",
    hour12: false,
  });
  $("#currentTime").text(timeString);
  currentTimeMinutes = now.getHours() * 60 + now.getMinutes();
}

function renderChannels() {
  const container = $("#channelsContainer");
  container.empty();

  channelsData.forEach((channel) => {
    const channelHtml = `
                    <div class="channel-card rounded-lg p-4 ${
                      channel.category
                    }" data-category="${channel.category}">
                        <div class="flex gap-4 items-center mb-4">
                            <img src="${channel.logo}" alt="${
      channel.name
    }" class="w-12 h-12 rounded-full">
                            <div>
                                <h3 class="text-xl font-bold">${
                                  channel.name
                                }</h3>
                                <span class="text-sm text-gray-400 capitalize">${getCategoryName(
                                  channel.category
                                )}</span>
                            </div>
                            <button class="px-4 py-2 mr-auto text-sm font-bold bg-red-600 rounded-lg transition-colors hover:bg-red-700 watch-channel" data-channel="${
                              channel.id
                            }">
                                <i class="mr-1 fas fa-play"></i>
                                شاهد القناة
                            </button>
                        </div>
                        
                        <div class="relative p-4 rounded-lg timeline-container">
                            <div class="time-indicator" id="indicator-${
                              channel.id
                            }"></div>
                            <div class="flex overflow-x-auto gap-2 pb-2 scroll-container">
                                ${channel.programs
                                  .map(
                                    (program) => `
                                    <div class="program-item ${
                                      program.current ? "active" : ""
                                    } min-w-[200px] p-3 rounded-lg"
                                         data-start="${program.start}" 
                                         data-end="${program.end}"
                                         data-channel="${channel.id}"
                                         data-program="${program.name}">
                                        <div class="mb-1 text-sm font-bold">${
                                          program.name
                                        }</div>
                                        <div class="mb-1 text-xs text-gray-300">${
                                          program.start
                                        } - ${program.end}</div>
                                        <div class="text-xs text-gray-400">${
                                          program.description
                                        }</div>
                                        ${
                                          program.current
                                            ? '<div class="mt-1 text-xs font-bold text-red-300">● يُعرض الآن</div>'
                                            : ""
                                        }
                                    </div>
                                `
                                  )
                                  .join("")}
                            </div>
                        </div>
                    </div>
                `;
    container.append(channelHtml);
  });
}

function updateTimeIndicators() {
  channelsData.forEach((channel) => {
    const indicator = $(`#indicator-${channel.id}`);
    const container = indicator.parent();
    const containerWidth = container.width();

    // Calculate position based on current time (simplified for demo)
    const position = ((currentTimeMinutes % 360) / 360) * containerWidth;
    indicator.css("left", position + "px");
  });
}

function getCategoryName(category) {
  return categories[category] || category;
}

function setupEventListeners() {
  // Category filters
  $(".category-filter").on("click", function () {
    const category = $(this).data("category");

    $(".category-filter")
      .removeClass("active bg-red-600")
      .addClass("bg-gray-700");
    $(this).removeClass("bg-gray-700").addClass("active bg-red-600");

    if (category === "all") {
      $(".channel-card").show();
    } else {
      $(".channel-card").hide();
      $(`.channel-card.${category}`).show();
    }
  });

  // Program click
  $(document).on("click", ".program-item", function () {
    const channelId = $(this).data("channel");
    const programName = $(this).data("program");
    const channel = channelsData.find((c) => c.id === channelId);
    const program = channel.programs.find((p) => p.name === programName);

    showProgramModal(program, channel);
  });

  // Watch channel button
  $(document).on("click", ".watch-channel", function (e) {
    e.stopPropagation();
    const channelId = $(this).data("channel");
    showLiveStream(channelId);
  });

  // Hero watch now button
  $("#watchNowBtn").on("click", function () {
    showLiveStream("mbc1");
  });

  // Modal controls
  $("#closeModal, #programModal").on("click", function (e) {
    if (e.target === this) {
      $("#programModal").addClass("hidden");
    }
  });

  $("#closeLiveStream, #liveStreamModal").on("click", function (e) {
    if (e.target === this || $(e.target).closest("#closeLiveStream").length) {
      $("#liveStreamModal").addClass("hidden");
    }
  });

  // Watch program button in modal
  $(document).on("click", "#watchProgramBtn", function () {
    $("#programModal").addClass("hidden");
    showLiveStream("mbc1");
  });
}

function showProgramModal(program, channel) {
  $("#modalTitle").text(program.name);
  $("#modalContent").html(`
                <div class="flex gap-4 items-center mb-4">
                    <img src="${channel.logo}" alt="${
    channel.name
  }" class="w-16 h-16 rounded-full">
                    <div>
                        <h4 class="text-lg font-bold">${channel.name}</h4>
                        <p class="text-gray-400">${program.start} - ${
    program.end
  }</p>
                    </div>
                </div>
                <p class="leading-relaxed text-gray-300">${
                  program.description
                }</p>
                <div class="flex gap-2 mt-4">
                    <span class="px-3 py-1 text-sm rounded-full category-tag">${getCategoryName(
                      channel.category
                    )}</span>
                    ${
                      program.current
                        ? '<span class="px-3 py-1 text-xs font-bold rounded-full live-indicator">● يُعرض الآن</span>'
                        : ""
                    }
                </div>
            `);
  $("#programModal").removeClass("hidden");
}

function showLiveStream(channelId) {
  $("#liveStreamModal").removeClass("hidden");

  // Simulate loading delay
  setTimeout(() => {
    $("#liveStreamModal .flex.items-center.justify-center").html(`
                    <div class="relative w-full max-w-4xl bg-black rounded-lg aspect-video">
                        <div class="flex absolute inset-0 justify-center items-center">
                            <div class="text-center">
                                <i class="mb-4 text-6xl text-red-500 fas fa-play-circle"></i>
                                <h3 class="mb-2 text-2xl font-bold">البث المباشر</h3>
                                <p class="text-gray-400">متصل بـ ${
                                  channelsData.find((c) => c.id === channelId)
                                    ?.name || "القناة"
                                }</p>
                                <div class="flex gap-4 justify-center items-center mt-4">
                                    <button class="p-3 rounded-full transition-colors bg-white/20 hover:bg-white/30">
                                        <i class="text-xl fas fa-pause"></i>
                                    </button>
                                    <button class="p-3 rounded-full transition-colors bg-white/20 hover:bg-white/30">
                                        <i class="text-xl fas fa-volume-up"></i>
                                    </button>
                                    <button class="p-3 rounded-full transition-colors bg-white/20 hover:bg-white/30">
                                        <i class="text-xl fas fa-expand"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="absolute bottom-4 left-4 px-3 py-2 rounded-lg bg-black/70">
                            <div class="text-sm font-bold">البث المباشر</div>
                            <div class="text-xs text-gray-300">جودة عالية HD</div>
                        </div>
                    </div>
                `);
  }, 2000);
}

// Auto-scroll timeline to current time position
function scrollToCurrentTime() {
  $(".scroll-container").each(function () {
    const container = $(this);
    const scrollLeft =
      ((currentTimeMinutes % 360) / 360) * container[0].scrollWidth;
    container.animate({ scrollLeft: scrollLeft }, 1000);
  });
}

// Keyboard shortcuts
$(document).keydown(function (e) {
  if (e.key === "Escape") {
    $("#programModal, #liveStreamModal").addClass("hidden");
  }
});

// Initialize scroll position
setTimeout(scrollToCurrentTime, 1000);

$("#watchNow").on("click", function (e) {
  e.preventDefault();
  const video = document.getElementById("heroVideo");

  // شغل الفيديو
  video.play();

  // ادخل وضع ملء الشاشة
  if (video.requestFullscreen) {
    video.requestFullscreen();
  } else if (video.webkitRequestFullscreen) {
    // Safari
    video.webkitRequestFullscreen();
  } else if (video.msRequestFullscreen) {
    // IE/Edge
    video.msRequestFullscreen();
  }
});
