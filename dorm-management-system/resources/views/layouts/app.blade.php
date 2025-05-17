<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'DMS')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #F5F5F5;
    }

    .top-nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 60px;
        padding: 0 20px;
        background-color: #fff;
        border-bottom: 1px solid #ddd;
    }

    .icon-circle,
    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #6f42c1;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
    }

    .avatar-circle-big {
        width: 45px;
        height: 45px;
        font-size: 20px;
        margin: 0 auto 8px;
    }

    .logo-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #4A4A4A;
        font-size: 24px;
        font-weight: bold;
    }

    .logo-img {
        height: 40px;
        margin-right: 10px;
    }

    .nav-icons {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    /* Стили для аватара и дропдауна */
    .avatar-wrapper {
        position: relative;
        display: inline-block;
    }

    .avatar-dropdown {
        display: none;
        position: absolute;
        top: 110%;
        right: 0;
        width: 180px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        z-index: 999;
        text-align: center;
    }

    .avatar-dropdown a {
        display: block;
        margin: 8px 0;
        text-decoration: none;
        color: #333;
        font-size: 14px;
    }

    .avatar-dropdown a:hover {
        text-decoration: underline;
    }

    /* Стили для новостей */
    .news-item {
        background-color: #B0A5D7;
        padding: 15px;
        color: #fff;
        max-width: 1500px;
        height: 240px;
        border-radius: 8px;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .news-item img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .news-item h3 {
        margin-bottom: 10px;
    }

    .news-item small {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    /* Стили модального окна */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        justify-content: center;
        align-items: center;
        transition: opacity 0.3s ease;
    }

    .modal-content {
        background-color: white;
        padding: 30px;
        border-radius: 16px;
        max-width: 2000px;
        width: 90%;
        position: relative;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        animation: modalFadeIn 0.3s ease-out;
        max-height: 90vh;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #B0A5D7 #f1f1f1;
    }

    .modal-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    #modalTitle {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        line-height: 1.3;
    }

    #modalContent {
        font-size: 16px;
        line-height: 1.6;
        color: #555;
        margin-bottom: 20px;
        white-space: pre-line;
    }

    #modalDate {
        display: block;
        font-size: 14px;
        color: #888;
        margin-top: 15px;
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 28px;
        font-weight: bold;
        color: #666;
        cursor: pointer;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .close-btn:hover {
        background: rgba(0, 0, 0, 0.1);
        color: #333;
        transform: rotate(90deg);
    }

    /* Стили для уведомлений */
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
    }

    .notification-wrapper {
        position: relative;
        z-index: 1000;
    }

    #notifications-panel {
        position: absolute;
        top: 100%;
        right: 0;
        width: 300px;
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 8px;
        z-index: 1000;
        display: none;
        margin-top: 10px;
    }
    /* Стили для уведомлений */
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .notification-wrapper {
        position: relative;
        z-index: 1000;
    }

    #notifications-panel {
        position: absolute;
        top: calc(100% + 10px);
        right: -10px;
        width: 350px;
        max-height: 500px;
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border-radius: 12px;
        z-index: 1000;
        display: none;
        overflow-y: auto;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .notifications-list {
        padding: 8px 0;
    }

    .notification-item {
        padding: 15px 20px;
        border-bottom: 1px solid #eef0f2;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item.unread {
        background-color: #f0f7ff;
        position: relative;
    }

    .notification-item.unread::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: #0d6efd;
        border-radius: 2px;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .notification-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .notification-message {
        color: #666;
        font-size: 13px;
        line-height: 1.4;
        margin-bottom: 8px;
    }

    .notification-time {
        font-size: 11px;
        color: #999;
        display: flex;
        align-items: center;
    }

    .notification-time::before {
        content: '\f017';
        font-family: 'Font Awesome 5 Free';
        margin-right: 5px;
        font-size: 10px;
    }

    /* Стили для скроллбара в панели уведомлений */
    #notifications-panel::-webkit-scrollbar {
        width: 6px;
    }

    #notifications-panel::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    #notifications-panel::-webkit-scrollbar-thumb {
        background: #B0A5D7;
        border-radius: 3px;
    }

    #notifications-panel::-webkit-scrollbar-thumb:hover {
        background: #9285c7;
    }

    /* Пустое состояние */
    .notification-item.empty {
        text-align: center;
        color: #999;
        padding: 30px 20px;
        font-style: italic;
    }

    /* Адаптивность */
    @media (max-width: 768px) {
        #notifications-panel {
            position: fixed;
            top: 70px;
            left: 10px;
            right: 10px;
            width: auto;
            max-height: calc(100vh - 80px);
        }
    }

    /* Анимации */
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Стили скроллбара */
    .modal-content::-webkit-scrollbar {
        width: 8px;
    }

    .modal-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .modal-content::-webkit-scrollbar-thumb {
        background: #B0A5D7;
        border-radius: 4px;
    }

    .modal-content::-webkit-scrollbar-thumb:hover {
        background: #9285c7;
    }

    /* Адаптивность */
    @media (max-width: 768px) {
        .modal-content {
            width: 95%;
            padding: 20px;
            margin: 20px;
        }

        .modal-image {
            height: 250px;
        }

        #modalTitle {
            font-size: 20px;
        }

        #modalContent {
            font-size: 14px;
        }
    }

</style>

<body>
{{-- Верхнее меню --}}
<div class="top-nav">
    <!-- Верхняя панель с логотипом и аватаром -->
    <a href="{{ route('student.dashboard') }}" class="logo-link">
        <img src="{{ asset('storage/icon/dark_icon.png') }}" alt="DMS Logo" class="logo-img">
        <span>DMS</span>
    </a>
    <div class="nav-icons">
        <!-- Иконки и переключатель языка -->
        <div class="text-message">
            @if(session('language_switched'))
                <p>You have switched to <span class="language">{{session('language_switched') === 'en' ? 'English' : (session('language_switched') == 'ru' ? "Russian" : "Kazakh")}} language</span></p>
            @endif
            <div class="text-messages">
                <p>{{__('messages.welcome')}}</p>
            </div>
        </div>
        <div>
            @include('components.language-switch')
        </div>
        <div class="notification-wrapper">
            <div class="icon-circle" style="background-color: #ffc107;">
                <i class="fas fa-bell"></i>
                <span class="notification-badge" style="display: none;"></span>
            </div>
            <div id="notifications-panel">
                <div class="notifications-list">
                    <!-- Уведомления будут добавлены через JavaScript -->
                </div>
            </div>
        </div>
        <div class="avatar-wrapper">
            <div class="icon-circle">
                {{ mb_substr(Auth::user()->name ?? 'A', 0, 1) }}
            </div>
            <div class="avatar-dropdown">
                <div class="avatar-circle avatar-circle-big">
                    {{ mb_substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div>{{ Auth::user()->name }}</div>
                @if(Auth::user()->role === 'student')
                    <a href="{{ route('student.personal') }}">{{ __('messages.my_profile') }}</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit">
                        <i class="fas fa-sign-out-alt"></i>{{ __('messages.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Боковая панель --}}
<div class="sidebar">
    <div class="sidebar-item" onclick="showNews()">
        <i class="fas fa-home"></i>
        <span>{{ __('messages.main') }}</span>
    </div>
    <!-- Другие элементы боковой панели -->
</div>

@yield('content')

<div class="main-content" id="see-news-section" style="display: block;">
    <h2>{{ __('messages.news') }}</h2>
    @isset($newsList)
        <div class="news-cards-container">
            @foreach($newsList as $news)
                <div class="news-item" onclick="openNewsModal({{ $news->id }})">
                    @if($news->image)
                        <img src="{{ asset('storage/' . $news->image) }}" alt="News Image">
                    @endif
                    <h3>{{ $news->title }}</h3>
                    <p>{{ Str::limit($news->content, 100) }}...</p>
                    <small>{{ $news->created_at->format('d.m.Y H:i') }}</small>
                </div>
            @endforeach
        </div>
    @else
        <p>{{ __('messages.no_news') }}</p>
    @endisset
</div>

{{-- Модальное окно для новости --}}
<div id="newsModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeNewsModal()">&times;</span>
        <h3 id="modalTitle"></h3>
        <img id="modalImage" src="" alt="News Image" class="modal-image">
        <p id="modalContent"></p>
        <small id="modalDate"></small>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationIcon = document.querySelector('.icon-circle');
        const notificationsPanel = document.getElementById('notifications-panel');
        const notificationsList = document.querySelector('.notifications-list');
        const notificationBadge = document.querySelector('.notification-badge');

        async function loadNotifications() {
            try {
                console.log('Начало загрузки уведомлений');

                const response = await fetch('/notifications');
                console.log('Ответ получен:', response);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const notifications = await response.json();
                console.log('Полученные уведомления:', notifications);

                notificationsList.innerHTML = '';
                let unreadCount = 0;

                if (notifications.length === 0) {
                    notificationsList.innerHTML = '<div class="notification-item">Нет уведомлений</div>';
                    return;
                }

                notifications.forEach(notification => {
                    if (!notification.read_at) {
                        unreadCount++;
                    }
                    const notificationElement = document.createElement('div');
                    notificationElement.className = `notification-item ${!notification.read_at ? 'unread' : ''}`;
                    notificationElement.innerHTML = `
                    <div class="notification-title">${notification.data.title}</div>
                    <div class="notification-message">${notification.data.message}</div>
                    <div class="notification-time">${new Date(notification.created_at).toLocaleString()}</div>
                `;
                    notificationElement.addEventListener('click', async () => {
                        try {
                            if (!notification.read_at) {
                                await fetch(`/notifications/${notification.id}/read`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });
                                notificationElement.classList.remove('unread');
                                updateNotificationBadge(--unreadCount);
                            }
                            window.location.href = "{{ route('student.personal') }}";
                        } catch (error) {
                            console.error('Ошибка при обработке уведомления:', error);
                        }
                    });
                    notificationsList.appendChild(notificationElement);
                });

                updateNotificationBadge(unreadCount);
            } catch (error) {
                console.error('Ошибка загрузки уведомлений:', error);
            }
        }

        function updateNotificationBadge(count) {
            if (count > 0) {
                notificationBadge.style.display = 'block';
                notificationBadge.textContent = count;
            } else {
                notificationBadge.style.display = 'none';
            }
        }

        notificationIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            const isVisible = notificationsPanel.style.display === 'block';
            notificationsPanel.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) {
                loadNotifications();
            }
        });

        document.addEventListener('click', function(e) {
            if (!notificationsPanel.contains(e.target) && !notificationIcon.contains(e.target)) {
                notificationsPanel.style.display = 'none';
            }
        });

        // Инициализация уведомлений при загрузке страницы
        loadNotifications();
    });
    function showNews() {
        hideAllSections();
        document.getElementById('see-news-section').style.display = 'block';
    }
    function openNewsModal(newsId) {
    const news = @json($newsList);
    const selectedNews = news.find(item => item.id === newsId);
    const modal = document.getElementById('newsModal');

    document.getElementById('modalTitle').innerText = selectedNews.title;
    const modalImage = document.getElementById('modalImage');
    modalImage.src = selectedNews.image ? '/storage/' + selectedNews.image : '';
    modalImage.style.display = selectedNews.image ? 'block' : 'none';

    document.getElementById('modalContent').innerText = selectedNews.content;
    document.getElementById('modalDate').innerText = new Date(selectedNews.created_at).toLocaleString('ru-RU');

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Запретить прокрутку фона

    // Плавное появление
    requestAnimationFrame(() => {
        modal.style.opacity = '1';
    });
}
    function closeNewsModal() {
        const modal = document.getElementById('newsModal');
        modal.style.opacity = '0';

        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = ''; // Восстановить прокрутку
        }, 300);
    }
    function hideAllSections() {
        const sections = document.querySelectorAll('.main-content');
        sections.forEach(section => {
            section.style.display = 'none';
        });
    }
    // Закрытие по клику вне модального окна
    document.getElementById('newsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeNewsModal();
        }
    });
    // Закрытие по нажатию Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNewsModal();
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        const avatarWrapper = document.querySelector('.avatar-wrapper');
        const avatarDropdown = document.querySelector('.avatar-dropdown');
        const avatarIcon = avatarWrapper.querySelector('.icon-circle');

        avatarIcon.addEventListener('click', function(e) {
            e.stopPropagation(); // Чтобы клик по аватару не всплывал
            avatarDropdown.style.display = avatarDropdown.style.display === 'block' ? 'none' : 'block';
        });

        document.addEventListener('click', function() {
            avatarDropdown.style.display = 'none';
        });
    });
</script>
</body>

</html>
