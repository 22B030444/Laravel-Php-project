@extends('layouts.app')

@section('content')
    <style>
        /* СБРОС */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #F5F5F5;
        }

        /* ОБЩИЕ СТИЛИ ДЛЯ КРУГЛЫХ ИКОНОК */
        .icon-circle, .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
        }
        .icon-circle i {
            font-size: 16px;
        }
        .avatar-circle {
            background-color: #6f42c1; /* Фиолетовый */
            font-weight: bold;
        }

        /* ВЕРХНЯЯ ПАНЕЛЬ */
        .top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            padding: 0 20px;
            background-color: #FFF;
            border-bottom: 1px solid #DDD;
        }
        .top-nav .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 24px;
            font-weight: bold;
            color: #4A4A4A;
        }
        /* Если есть логотип-изображение, раскомментируй:
        .top-nav .logo img {
            height: 40px;
        } */
        .top-nav .nav-icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* ОБЁРТКА ДЛЯ АВАТАРА И МЕНЮ */
        .avatar-wrapper {
            position: relative;
            display: inline-block;
        }
        /* Меню скрыто по умолчанию */
        .avatar-dropdown {
            display: none;
            position: absolute;
            top: 110%;
            right: 0;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            width: 160px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 999;
            text-align: center;
        }
        /* При наведении на .avatar-wrapper показываем меню */
        .avatar-wrapper:hover .avatar-dropdown {
            display: block;
        }

        .avatar-name {
            font-weight: bold;
            color: #4A4A4A;
            margin-bottom: 8px;
        }
        .avatar-dropdown a {
            display: block;
            text-decoration: none;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .avatar-dropdown a:hover {
            text-decoration: underline;
        }

        /* ЛЕВАЯ ПАНЕЛЬ */
        .sidebar {
            position: fixed;
            top: 60px; /* высота шапки */
            left: 0;
            width: 200px;
            height: calc(100vh - 60px);
            background-color: #FFF;
            border-right: 1px solid #DDD;
            padding-top: 20px;
        }
        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
        }
        .sidebar-item:hover {
            background-color: #EFEFEF;
            cursor: pointer;
        }
        .sidebar-item i {
            font-size: 18px;
            color: #4A4A4A;
        }

        /* ОСНОВНОЙ КОНТЕНТ */
        .main-content {
            margin-left: 200px; /* отступ под ширину сайдбара */
            padding: 20px;
            padding-top: 80px;  /* чтобы контент не лез под шапку */
        }
        .main-content h2 {
            margin-bottom: 20px;
            color: #4A4A4A;
        }

        /* СТИЛИ ДЛЯ НОВОСТЕЙ */
        .news-item {
            background-color: #B0A5D7; /* Фиолетовый */
            padding: 35px;
            color: #fff;
            max-width: 1200px; /* или 200px, на твой вкус */
            height: 250px;     /* сохраняем пропорции */
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .news-item h3 {
            margin-bottom: 10px;
            color: #fff;
        }
        .news-item img {
            width: 100px;       /* Force a wide width */
            height: 100px;      /* Make it half as tall => "landscape" shape */
            object-fit: cover;  /* Crop edges if the ratio doesn't match exactly */
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .news-item small {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        /* Увеличенный кружок с буквой внутри меню (опционально) */
        .avatar-circle-big {
            width: 50px;
            height: 50px;
            font-size: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-bottom: 8px;
        }

        /* Кнопка выхода (с иконкой) */
        .logout-form button {
            background: none;
            border: none;
            color: #333;
            font-size: 0.9rem;
            cursor: pointer;
            /*display: flex;*/
            /*justify-content: center;!* иконка + текст в одну строку *!*/
            /*align-items: center;*/
            gap: 6px;            /* отступ между иконкой и текстом */
        }
        .logout-form button:hover {
            text-decoration: underline;
        }
        /* Стили боковой панели */
        .housing-sidebar {
            position: fixed;
            right: -320px;
            top: 60px;
            width: 300px;
            height: calc(100vh - 60px);
            background-color: #FFF;
            border-left: 1px solid #DDD;
            padding: 20px;
            transition: right 0.3s ease-in-out;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .housing-sidebar.open {
            right: 0;
        }
        .housing-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .close-btn {
            cursor: pointer;
            font-size: 20px;
            border: none;
            background: none;
        }
        .housing-form label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        .housing-form select, .housing-form button {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        .hidden {
            display: none !important;
        }
    </style>

    {{-- ВЕРХНЯЯ ПАНЕЛЬ --}}
{{--    --}}

    {{-- ЛЕВАЯ ПАНЕЛЬ --}}
    <div class="sidebar">
        <div class="sidebar-item" onclick="toggleSection('news')">
            <i class="fas fa-home"></i>
            <span>Лента</span>
        </div>
        <div class="sidebar-item" onclick="toggleSection('housing')">
            <i class="fas fa-bed"></i>
            <span>Проживание</span>
        </div>
        <div class="sidebar-item">
            <i class="fas fa-store"></i>
            <span>Купи-Продай</span>
        </div>
    </div>

    {{-- Раздел новостей --}}
    <div class="main-content" id="news-section">
        <h2>Новости</h2>
        @isset($newsList)
            @forelse($newsList as $news)
                <div class="news-item">
                    @if($news->image)
                        <img src="{{ asset('storage/' . $news->image) }}" alt="News Image">
                    @endif
                    <h3>{{ $news->title }}</h3>
                    <p>{{ $news->content }}</p>
                    <small>{{ $news->created_at->format('d.m.Y H:i') }}</small>
                </div>
            @empty
                <p>Нет новостей</p>
            @endforelse
        @endisset
    </div>

    {{-- Боковая панель заявки на проживание --}}
    <div class="housing-sidebar" id="housing-sidebar">
        <div class="housing-header">
            <h3>Выбор комнаты</h3>
            <button class="close-btn" onclick="toggleSection('news')">&times;</button>
        </div>
        <form class="housing-form" action="{{ route('booking.store') }}" method="POST">
            @csrf
            <select name="building_id" id="building">
                <option value="">Выберите корпус</option>
                @foreach($buildings as $building)
                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                @endforeach
            </select>

            <select name="floor" id="floor" disabled>
                <option value="">Сначала выберите корпус</option>
            </select>

            <select name="room_id" id="room" disabled>
                <option value="">Сначала выберите этаж</option>
            </select>

            <button type="submit">Заселиться</button>
        </form>
    </div>

    <script>
        function toggleSection(section) {
            console.log("Функция toggleSection вызвана:", section);
            const newsSection = document.getElementById('news-section');
            const housingSidebar = document.getElementById('housing-sidebar');

            if (!newsSection || !housingSidebar) {
                console.error("ID не найден.");
                return;
            }

            if (section === 'housing') {
                console.log("Открываем sidebar 'housing'");
                housingSidebar.classList.add('open');
                newsSection.classList.add('hidden');
            } else {
                console.log("Закрываем sidebar 'housing'");
                housingSidebar.classList.remove('open');
                newsSection.classList.remove('hidden');
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const buildingSelect = document.getElementById("building");
            const floorSelect = document.getElementById("floor");
            const roomSelect = document.getElementById("room");

            async function loadFloors(buildingId) {
                if (!buildingId) {
                    floorSelect.innerHTML = '<option value="">Сначала выберите корпус</option>';
                    floorSelect.disabled = true;
                    return;
                }

                try {
                    console.log(`Загружаем этажи для корпуса ID: ${buildingId}`);
                    const response = await fetch(`/floors/${buildingId}`);
                    const data = await response.json();
                    console.log("Этажи:", data);

                    if (!data || data.length === 0) {
                        floorSelect.innerHTML = '<option value="">Нет доступных этажей</option>';
                        floorSelect.disabled = true;
                        return;
                    }

                    floorSelect.innerHTML = '<option value="">Выберите этаж</option>';
                    data.forEach(floor => {
                        floorSelect.innerHTML += `<option value="${floor}">${floor}</option>`;
                    });
                    floorSelect.disabled = false;
                } catch (error) {
                    console.error("Ошибка загрузки этажей:", error);
                }
            }

            async function loadRooms(buildingId, floor) {
                if (!floor) {
                    roomSelect.innerHTML = '<option value="">Сначала выберите этаж</option>';
                    roomSelect.disabled = true;
                    return;
                }

                try {
                    console.log(`Загружаем комнаты для корпуса ID: ${buildingId}, этаж: ${floor}`);
                    const response = await fetch(`/rooms/${buildingId}/${floor}`);
                    const data = await response.json();
                    console.log("Комнаты:", data);

                    if (!data || data.length === 0) {
                        roomSelect.innerHTML = '<option value="">Нет доступных комнат</option>';
                        roomSelect.disabled = true;
                        return;
                    }

                    roomSelect.innerHTML = '<option value="">Выберите комнату</option>';
                    data.forEach(room => {
                        roomSelect.innerHTML += `<option value="${room.id}">${room.room_number}</option>`;
                    });

                    roomSelect.disabled = false;
                } catch (error) {
                    console.error("Ошибка загрузки комнат:", error);
                }
            }

            // Добавляем обработчики событий
            buildingSelect.addEventListener("change", function () {
                const buildingId = this.value;
                loadFloors(buildingId);
                roomSelect.innerHTML = '<option value="">Сначала выберите этаж</option>';
                roomSelect.disabled = true;
            });

            floorSelect.addEventListener("change", function () {
                const buildingId = buildingSelect.value;
                const floor = this.value;
                loadRooms(buildingId, floor);
            });
        });

    </script>

@endsection
