import './bootstrap';


const userId = document.querySelector('meta[name="user_id"]').getAttribute('content');
window.Echo.private(`user.${userId}`)
    .listen('.new-employee-notification', (e) => {
        alert(e.message);
    });
