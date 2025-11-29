// API Configuration
const API_BASE = window.location.origin + '/api/v1';
let channelsData = [];
let categoriesData = [];
let currentTimeMinutes = 0;

// Initialize the page
$(document).ready(async function () {
  updateCurrentTime();
  
  // Load categories first, then channels
  await loadCategories();
  await loadChannels();
  
  updateTimeIndicators();

  // Update time every minute
  setInterval(updateCurrentTime, 60000);
  setInterval(updateTimeIndicators, 60000);

  // Event listeners
  setupEventListeners();
  
  // Initialize hero video
  initHeroVideo();
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

// Load categories from API
async function loadCategories() {
  try {
    const response = await fetch(`${API_BASE}/live-tv/categories`);
    const data = await response.json();
    
    if (data.success && data.data) {
      categoriesData = data.data;
      renderCategories();
    }
  } catch (error) {
    console.error('Error loading categories:', error);
  }
}

// Render categories filter buttons
function renderCategories() {
  const container = $('#categoriesContainer');
  container.empty();
  
  // Add "All" button
  container.append(`
    <button class="px-4 py-2 font-medium text-white whitespace-nowrap bg-red-600 rounded-full category-filter active" data-category="all">
      جميع القنوات
    </button>
  `);
  
  // Add category buttons
  categoriesData.forEach(category => {
    container.append(`
      <button class="px-4 py-2 font-medium text-white whitespace-nowrap bg-gray-700 rounded-full category-filter hover:bg-gray-600" data-category="${category.id}">
        ${category.name_ar}
      </button>
    `);
  });
}

// Load channels from API
async function loadChannels() {
  try {
    const response = await fetch(`${API_BASE}/live-tv/channels`);
    const data = await response.json();
    
    if (data.success && data.data) {
      channelsData = data.data;
      
      // Load EPG for each channel
      for (const channel of channelsData) {
        await loadChannelEPG(channel);
      }
      
      renderChannels();
    } else {
      showError('فشل في تحميل القنوات');
    }
  } catch (error) {
    console.error('Error loading channels:', error);
    showError('حدث خطأ في تحميل القنوات');
  }
}

// Load EPG for a channel
async function loadChannelEPG(channel) {
  try {
    const response = await fetch(`${API_BASE}/live-tv/channels/${channel.id}/programs`);
    const data = await response.json();
    
    if (data.success && data.data) {
      channel.programs = data.data.map(program => ({
        name: program.title_ar,
        start: new Date(program.start_time).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit', hour12: false }),
        end: new Date(program.end_time).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit', hour12: false }),
        description: program.description_ar || 'لا يوجد وصف',
        current: isCurrentProgram(program.start_time, program.end_time)
      }));
    } else {
      channel.programs = [];
    }
  } catch (error) {
    console.error(`Error loading EPG for channel ${channel.id}:`, error);
    channel.programs = [];
  }
}

// Check if program is currently airing
function isCurrentProgram(startTime, endTime) {
  const now = new Date();
  const start = new Date(startTime);
  const end = new Date(endTime);
  return now >= start && now <= end;
}

function renderChannels() {
  const container = $("#channelsContainer");
  container.empty();
  
  if (!channelsData || channelsData.length === 0) {
    container.html('<div class="text-center text-gray-400 py-10">لا توجد قنوات متاحة</div>');
    return;
  }
  
  // Show only channels that match selected category
  const activeCategory = $('.category-filter.active').data('category');

  channelsData.forEach((channel) => {
    // Skip if category filter is active and doesn't match
    if (activeCategory && activeCategory !== 'all' && channel.category_id != activeCategory) {
      return;
    }
    
    // Show watch button only for online channels
    const watchButton = channel.stream_health_status === 'online' ? `
      <button class="px-4 py-2 mr-auto text-sm font-bold bg-red-600 rounded-lg transition-colors hover:bg-red-700 watch-channel" data-channel="${channel.id}">
        <i class="mr-1 fas fa-play"></i>
        شاهد القناة
      </button>
    ` : '';
    
    const channelHtml = `
                    <div class="channel-card rounded-lg p-4" data-category="${channel.category_id}" data-channel-id="${channel.id}">
                        <div class="flex gap-4 items-center mb-4">
                            <img src="${channel.logo_url || '/imgs/default-channel.png'}" alt="${channel.name_ar}" class="w-12 h-12 rounded-full object-cover" onerror="this.src='/imgs/default-channel.png'">
                            <div>
                                <h3 class="text-xl font-bold">${channel.name_ar}</h3>
                                <span class="text-sm text-gray-400">${channel.stream_health_status === 'online' ? '🟢 مباشر' : channel.stream_health_status === 'offline' ? '🔴 غير متصل' : '⚪ غير معروف'}</span>
                            </div>
                            ${watchButton}
                        </div>
                        
                        <div class="relative p-4 rounded-lg timeline-container">
                            <div class="time-indicator" id="indicator-${channel.id}"></div>
                            <div class="flex overflow-x-auto gap-2 pb-2 scroll-container">
                                ${channel.programs && channel.programs.length > 0 ? channel.programs.map(program => `
                                    <div class="program-item ${program.current ? "active" : ""} min-w-[200px] p-3 rounded-lg"
                                         data-start="${program.start}" 
                                         data-end="${program.end}"
                                         data-channel="${channel.id}"
                                         data-program="${program.name}">
                                        <div class="mb-1 text-sm font-bold">${program.name}</div>
                                        <div class="mb-1 text-xs text-gray-300">${program.start} - ${program.end}</div>
                                        <div class="text-xs text-gray-400">${program.description}</div>
                                        ${program.current ? '<div class="mt-1 text-xs font-bold text-red-300">● يُعرض الآن</div>' : ""}
                                    </div>
                                `).join("") : '<div class="text-sm text-gray-400">لا توجد برامج متاحة</div>'}
                            </div>
                        </div>
                    </div>
                `;
    container.append(channelHtml);
  });
  
  // Update time indicators and scroll to current programs after rendering
  setTimeout(() => {
    updateTimeIndicators();
    scrollToCurrentTime();
  }, 100);
}

function showError(message) {
  $("#channelsContainer").html(`<div class="text-center text-red-400 py-10">${message}</div>`);
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

function setupEventListeners() {
  // Category filters
  $(document).on("click", ".category-filter", function () {
    const categoryId = $(this).data("category");

    $(".category-filter")
      .removeClass("active bg-red-600")
      .addClass("bg-gray-700");
    $(this).removeClass("bg-gray-700").addClass("active bg-red-600");

    // Re-render channels with new filter
    renderChannels();
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
  $("#watchNowBtn, #watchNow").on("click", function (e) {
    e.preventDefault();
    if (channelsData && channelsData.length > 0) {
      showLiveStream(channelsData[0].id);
    }
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
    const channelId = $(this).data("channel");
    $("#programModal").addClass("hidden");
    showLiveStream(channelId);
  });
}

function showProgramModal(program, channel) {
  $("#modalTitle").text(program.name);
  $("#modalContent").html(`
    <div class="flex gap-4 items-center mb-4">
      <img src="${channel.logo_url || '/imgs/default-channel.png'}" alt="${channel.name_ar}" 
           class="w-16 h-16 rounded-full object-cover" 
           onerror="this.src='/imgs/default-channel.png'">
      <div>
        <h4 class="text-lg font-bold">${channel.name_ar}</h4>
        <p class="text-gray-400">${program.start} - ${program.end}</p>
      </div>
    </div>
    <p class="leading-relaxed text-gray-300">${program.description}</p>
    <div class="flex gap-2 mt-4">
      ${program.current ? '<span class="px-3 py-1 text-xs font-bold rounded-full live-indicator">● يُعرض الآن</span>' : ''}
    </div>
  `);
  
  // Update watch button to use correct channel ID
  $("#watchProgramBtn").data("channel", channel.id);
  
  $("#programModal").removeClass("hidden");
}

async function showLiveStream(channelId) {
  $("#liveStreamModal").removeClass("hidden");

  try {
    // Get stream URL from API
    const response = await fetch(`${API_BASE}/live-tv/channels/${channelId}/stream`);
    const data = await response.json();
    
    if (data.success && data.data && data.data.stream_url) {
      const channel = channelsData.find((c) => c.id == channelId);
      
      $("#liveStreamModal .flex.items-center.justify-center").html(`
        <div class="relative w-full max-w-4xl bg-black rounded-lg aspect-video">
          <video id="liveVideo" class="w-full h-full" controls autoplay>
            <source src="${data.data.stream_url}" type="application/x-mpegURL">
            متصفحك لا يدعم تشغيل الفيديو
          </video>
          <div class="absolute bottom-4 left-4 px-3 py-2 rounded-lg bg-black/70">
            <div class="text-sm font-bold">${channel?.name_ar || 'البث المباشر'}</div>
            <div class="text-xs text-gray-300">جودة عالية HD</div>
          </div>
        </div>
      `);
      
      // Initialize HLS.js for HLS streams
      const video = document.getElementById('liveVideo');
      if (Hls.isSupported()) {
        const hls = new Hls();
        hls.loadSource(data.data.stream_url);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
          video.play();
        });
      } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = data.data.stream_url;
        video.addEventListener('loadedmetadata', function() {
          video.play();
        });
      }
    } else {
      throw new Error('فشل في تحميل البث');
    }
  } catch (error) {
    console.error('Error loading stream:', error);
    $("#liveStreamModal .flex.items-center.justify-center").html(`
      <div class="text-center">
        <i class="mb-4 text-6xl text-red-500 fas fa-exclamation-circle"></i>
        <h3 class="mb-2 text-2xl font-bold">حدث خطأ</h3>
        <p class="text-gray-400">لم نتمكن من تحميل البث المباشر</p>
      </div>
    `);
  }
}

// Auto-scroll timeline to current program
function scrollToCurrentTime() {
  $(".channel-card").each(function () {
    const scrollContainer = $(this).find('.scroll-container');
    const currentProgram = $(this).find('.program-item.active');
    
    if (currentProgram.length > 0 && scrollContainer.length > 0) {
      // Get program position
      const programPosition = currentProgram[0].offsetLeft;
      const programWidth = currentProgram.outerWidth();
      const containerWidth = scrollContainer.width();
      
      // Center the current program in view
      const scrollPosition = programPosition - (containerWidth / 2) + (programWidth / 2);
      scrollContainer.scrollLeft(Math.max(0, scrollPosition));
    }
  });
}

// Keyboard shortcuts
$(document).keydown(function (e) {
  if (e.key === "Escape") {
    $("#programModal, #liveStreamModal").addClass("hidden");
  }
});

// Scroll position will be initialized after channels are rendered

// Initialize hero video with HLS.js
function initHeroVideo() {
  const video = document.getElementById('heroVideo');
  const videoSrc = video.getAttribute('src');
  
  if (!videoSrc) return;
  
  if (Hls.isSupported()) {
    const hls = new Hls();
    hls.loadSource(videoSrc);
    hls.attachMedia(video);
    hls.on(Hls.Events.MANIFEST_PARSED, function () {
      video.play().catch(e => console.log('Autoplay prevented:', e));
    });
  } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
    video.addEventListener('loadedmetadata', function () {
      video.play().catch(e => console.log('Autoplay prevented:', e));
    });
  }
  
  // Mute/Unmute button
  const muteBtn = document.getElementById('muteBtn');
  if (muteBtn) {
    muteBtn.addEventListener('click', function() {
      if (video.muted) {
        video.muted = false;
        muteBtn.setAttribute('data-state', 'unmuted');
        muteBtn.innerHTML = '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"></path></svg>';
      } else {
        video.muted = true;
        muteBtn.setAttribute('data-state', 'muted');
        muteBtn.innerHTML = '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"></path></svg>';
      }
    });
  }
}
