@extends('layouts.app')

@section('content')
    <link href="{{ asset('css/adminDashboard.css') }}" rel="stylesheet">

    <div class="sidebar">
        <div class="sidebar-item" onclick="showNews()">
            <i class="fas fa-home"></i>
            <span>{{__('messages.main')}}</span>
        </div>
        <div class="sidebar-item" onclick="seeNews()">
            <i class="fa-solid fa-newspaper"></i>
            <span>{{ __('messages.news') }}</span>
        </div>
        <div class="sidebar-item" onclick="showUsers()">
            <i class="fas fa-user"></i>
            <span>{{ __('messages.users') }}</span>
        </div>
    </div>

    <!-- СЕКЦИЯ "Новости" (CRUD), по умолчанию скрыта -->
    <div class="main-content" id="news-section" style="display: none;">
        <div class="container">
            <h2>{{ __('messages.manage_news') }}</h2>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Кнопка для показа формы создания новости -->
            <a href="javascript:void(0)" onclick="CreateNews()" class="btn btn-primary mb-3">{{ __('messages.create_news') }}</a>

            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($newsList as $news)
                    <tr>
                        <td>{{ $news->title }}</td>
                        <td>{{ $news->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm"
                                    onclick='EditNews({{ $news->id }}, {!! json_encode($news->title) !!}, {!! json_encode(strip_tags($news->content)) !!})'>
                                {{ __('messages.edit') }}
                            </button>
                            <form action="{{ route('admin.news.destroy', $news->id) }}"
                                  method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.confirm_delete') }}')">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">{{ __('messages.no_news') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- ========== Секция создания новости (скрыта) ========== -->
    <div class="main-content" id="create-news-section" style="display: none;">
        <div class="container">
            <h1>{{ __('messages.create_news') }}</h1>
            <div class="create-news-section">
                <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="form-label">{{ __('messages.title') }}</label>
                    <input type="text" name="title" class="form-control" required>

                    <label class="form-label">{{ __('messages.content') }}</label>
                    <textarea name="content" class="form-control" rows="5" required></textarea>

                    <label class="form-label">{{ __('messages.image_optional') }}</label>
                    <input type="file" name="image" class="form-control">

                    <button type="submit" class="btn-success">{{ __('messages.create') }}</button>
                </form>
            </div>
        </div>
    </div>
    <!-- ========== Секция редактирования новости (скрыта) ========== -->
    <div class="main-content" id="edit-news-section" style="display: none;">
        <div class="container">
            <h1>{{ __('messages.edit_news') }}</h1>
            <div class="create-news-section">
                <form id="editNewsForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    <label class="form-label">{{ __('messages.title') }}</label>
                    <input type="text" id="edit-title" name="title" class="form-control" required>

                    <label class="form-label">{{ __('messages.content') }}</label>
                    <textarea id="edit-content" name="content" class="form-control" rows="5" required></textarea>

                    <label class="form-label">{{ __('messages.image_optional') }}</label>
                    <input type="file" name="image" class="form-control">

                    <button type="submit" class="btn-success">{{ __('messages.save') }}</button>
                    <button type="button" class="plus-button" onclick="cancelEdit()">{{ __('messages.cancel') }}</button>
                </form>
            </div>
        </div>
    </div>
    <!-- ========== Секция пользователей (скрыта) ========== -->
    <div class="main-content" id="users-section" style="display: none;">
        <div class="heading-row">
            <h2>{{ __('messages.user_list') }}</h2>
            <button class="plus-button" onclick="openModal()">+</button>
        </div>
        <!-- Фильтр / поиск -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="user-filter">
            <input type="text" name="filter_id" placeholder="{{ __('messages.id') }}" value="{{ request('filter_id') }}">
            <input type="text" name="filter_name" placeholder="{{ __('messages.full_name') }}" value="{{ request('filter_name') }}">
            <input type="text" name="filter_email" placeholder="{{ __('messages.email') }}" value="{{ request('filter_email') }}">
            <input type="text" name="filter_role" placeholder="{{ __('messages.status') }}" value="{{ request('filter_role') }}">
            <button type="submit" class="btn-primary">{{ __('messages.search') }}</button>
        </form>

        <!-- Таблица пользователей -->
        <table class="users-table">
            <thead>
            <tr>
                <th>{{ __('messages.id') }}</th>
                <th>{{ __('messages.full_name') }}</th>
                <th>{{ __('messages.email') }}</th>
                <th>{{ __('messages.status') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->user_id }}</td>
                    <td>
                        <a href="javascript:void(0)"
                           style="text-decoration: none; color: var(--text-color);"
                           onclick="viewUserDetail({{ $user->id }})">
                            {{ $user->name }}
                        </a>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                </tr>
            @empty
                <tr><td colspan="4">{{ __('messages.no_users') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <!-- ========== Секция деталей пользователя (скрыта) ========== -->
    <div class="main-content user-details-section" id="user-details-section" style="display: none;">
        <button onclick="showUsers()" class="btn-primary" style="margin-bottom: 20px;">
            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
        </button>
        <h2>{{ __('messages.user_details') }}</h2>
        <div class="user-details-card">
            <!-- Фото -->
            <div class="user-photo" id="user-photo">
                <img src="https://via.placeholder.com/180x180?text=No+Photo" alt="{{ __('messages.user_photo') }}">
            </div>
            <!-- Информация -->
            <div class="user-info">
                <h2 id="detail-name">{{ __('messages.full_name') }}</h2>
                <div class="status">{{ __('messages.status') }}: <span id="detail-role"></span></div>
                <div class="personal-location">
                    @if($user->acceptedBooking)
                        {{ __('messages.building') }}: {{ $user->acceptedBooking->building->name }}<br>
                        {{ __('messages.address') }}: {{ $user->acceptedBooking->building->address }}<br>
                        {{ __('messages.floor') }}: {{ $user->acceptedBooking->room->floor }}<br>
                        {{ __('messages.room') }}: {{ $user->acceptedBooking->room->room_number }}
                    @else
                        <p>{{ __('messages.not_settled') }}</p>
                    @endif
                </div>

                <!-- Форма обновления -->
                <form id="userUpdateForm" class="user-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    <div>
                        <label>{{ __('messages.id') }}</label>
                        <input type="text" name="user_id" id="detail-user_id">
                    </div>
                    <div>
                        <label>{{ __('messages.phone') }}</label>
                        <input type="text" name="phone" id="detail-phone">
                    </div>
                    <div>
                        <label>{{ __('messages.email') }}</label>
                        <input type="email" name="email" id="detail-email">
                    </div>
                    <div>
                        <label>{{ __('messages.password') }}</label>
                        <input type="password" disabled value="********">
                    </div>
                    <div>
                        <label>{{ __('messages.photo') }}</label>
                        <input type="file" name="photo">
                    </div>

                    <div class="actions" style="grid-column: 1 / span 2;">
                        <button type="submit" class="btn-success">{{ __('messages.save_changes') }}</button>
                    </div>
                </form>

                <!-- Кнопка "Удалить пользователя" -->
                <form id="userDeleteForm"
                      action="{{ route('admin.users.destroy', $user->id) }}"
                      method="POST"
                      style="margin-top: 10px;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                        {{ __('messages.delete_user') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- ========== Модальное окно (создание пользователя) ========== -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-content">
            <button class="close-button" onclick="closeModal()">×</button>
            <h2>{{ __('messages.create_user') }}</h2>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $err)
                        <p>{{ $err }}</p>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <label class="form-label">{{ __('messages.full_name') }}</label>
                <input type="text" name="name" class="form-control" required>
                <label class="form-label">{{ __('messages.id') }}</label>
                <input type="text" name="user_id" class="form-control" value="..." required>
                <label class="form-label">{{ __('messages.email') }}</label>
                <input type="email" name="email" class="form-control" value="..." required>
                <label class="form-label">{{ __('messages.password') }}</label>
                <input type="password" name="password" class="form-control" required>
                <label class="form-label">{{ __('messages.role') }}</label>
                <select name="role" class="form-control">
                    <option value="student" @if(old('role') === 'student') selected @endif>{{ __('messages.student') }}</option>
                    <option value="manager" @if(old('role') === 'manager') selected @endif>{{ __('messages.manager') }}</option>
                    <option value="employee" @if(old('role') === 'employee') selected @endif>{{ __('messages.employee') }}</option>
                    <option value="admin" @if(old('role') === 'admin') selected @endif>{{ __('messages.admin') }}</option>
                </select>
                <button type="submit" class="btn-success" style="margin-top: 10px;">{{ __('messages.create') }}</button>
            </form>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showNews();
            @if($errors->any())
            openModal();
            @endif
            @if(session('successType') === 'user_created')
            closeModal();
            showUsers();
            @elseif(session('successType') === 'news_created')
            showNews();
            @elseif(session('successType') === 'news_updated')
            showNews();
            @elseif(session('successType') === 'news_deleted')
            showNews();
            @elseif(session('successType') === 'user_searched')
            showUsers();
            @endif
        });
        function hideAllSections() {
            document.getElementById('users-section').style.display = 'none';
            document.getElementById('news-section').style.display = 'none';
            document.getElementById('see-news-section').style.display = 'none';
            document.getElementById('create-news-section').style.display = 'none';
            document.getElementById('edit-news-section').style.display = 'none';
            document.getElementById('user-details-section').style.display = 'none';
        }
        function showUsers() {
            hideAllSections();
            document.getElementById('users-section').style.display = 'block';
        }

        function seeNews(){
            hideAllSections();
            document.getElementById('news-section').style.display = 'block';
        }
        function CreateNews() {
            hideAllSections();
            document.getElementById('create-news-section').style.display = 'block';
        }
        function EditNews(id, title, content) {
            hideAllSections();
            document.getElementById('edit-news-section').style.display = 'block';
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-content').value = content;
            document.getElementById('editNewsForm').action = '{{ url("admin/dashboard/news") }}/' + id;
        }
        function cancelEdit() {
            document.getElementById('edit-news-section').style.display = 'none';
            showNews();
        }
        function openModal() {
            document.getElementById('modalOverlay').style.display = 'flex';
        }
        function closeModal() {
            document.getElementById('modalOverlay').style.display = 'none';
        }
        async function viewUserDetail(userId) {
            hideAllSections();
            let url = '/admin/dashboard/users/' + userId + '/json';
            try {
                let response = await fetch(url);
                if (!response.ok) {
                    throw new Error('Ошибка при загрузке пользователя');
                }
                let data = await response.json();
                document.getElementById('detail-name').textContent = data.name || '';
                document.getElementById('detail-role').textContent = data.role || '';
                if (data.photo) {
                    document.getElementById('user-photo').innerHTML =
                        `<img src="/storage/${data.photo}" alt="User Photo">`;
                } else {
                    document.getElementById('user-photo').innerHTML =
                        `<img src="https://via.placeholder.com/180x180?text=No+Photo" alt="User Photo">`;
                }
                document.getElementById('detail-user_id').value = data.user_id || '';
                document.getElementById('detail-phone').value = data.phone || '';
                document.getElementById('detail-email').value = data.email || '';
                document.getElementById('userUpdateForm').action = '/admin/dashboard/users/' + data.id;
                document.getElementById('userDeleteForm').action = '/admin/dashboard/users/' + data.id;
                document.getElementById('user-details-section').style.display = 'block';
            } catch (error) {
                alert(error.message);
                showUsers();
            }
        }
    </script>
@endsection
