const notificationMenu = document.getElementById("notification-dropdown");
const notificationButton = document.getElementById("notification-button");
const sessionId = document.getElementById("session_id");

async function fetchAndDisplayNotifications() {
    const notifications = await fetchNotifications(sessionId.value);
    displaynotifications(notifications);
}

notificationButton.addEventListener('mouseenter', fetchAndDisplayNotifications);

async function fetchNotifications(sessionId) {
    try {
        const response = await fetch(`/notification/getNotifications?id=${encodeURIComponent(sessionId)}`);
        const notifications = await response.json();
        return notifications;
    } catch (error) {
        console.error('Error fetching notifications:', error);
        return [];
    }
}

function displaynotifications(notifications) {
    notificationMenu.innerHTML = '';
    notifications.forEach(notification => {
        const notificationElement = document.createElement('div');
        notificationElement.classList.add('notification');
        notificationElement.id = notification.idNotif;

        const nameSpan = document.createElement('span');
        nameSpan.textContent = `${notification.name} ${notification.lastname} a commenté votre topic "${notification.title}" le ${notification.commentCreatedAt}`;
        nameSpan.className = 'notification-name';
        notificationElement.appendChild(nameSpan);

        const deleteSpan = document.createElement('span');
        deleteSpan.textContent = "Supprimer";
        deleteSpan.style.fontFamily = 'Arial, sans-serif';
        deleteSpan.style.fontWeight = 'bold';
        deleteSpan.style.cursor = 'pointer';
        deleteSpan.className = 'notification-delete';
        deleteSpan.style.float = 'right';
        deleteSpan.setAttribute("onclick", `deleteNotification(${notification.idNotif});`);
        notificationElement.appendChild(deleteSpan);

        notificationMenu.appendChild(notificationElement);
    });
}

async function deleteNotification(idNotif) {
    try {
        const response = await fetch(`/notification/delete?id=${encodeURIComponent(idNotif)}`);
        const deleted = await response.json();
        if (deleted === 'OK') {
            var notifElement = document.getElementById(idNotif);
            notifElement.classList.add("hidden");
        }
    } catch (error) {
        console.error('Error fetching notifications:', error);
        return [];
    }
}