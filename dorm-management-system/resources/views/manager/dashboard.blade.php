@extends('layouts.app')

@section('content')
    <link href="{{ asset('css/managerDashboard.css') }}" rel="stylesheet">

   <!-- Sidebar -->
   <div class="sidebar">
       <div class="sidebar-item" onclick="showNews()"><i class="fas fa-home"></i><span>{{ __('messages.main') }}</span></div>
       <div class="sidebar-item" onclick="seeNews()"><i class="fas fa-newspaper"></i><span>{{ __('messages.news') }}</span></div>
       <div class="sidebar-item" onclick="showPersonal()"><i class="fas fa-user"></i><span>{{ __('messages.personal_data') }}</span></div>
       <div class="sidebar-item" onclick="showUsers()"><i class="fas fa-user"></i><span>{{ __('messages.users') }}</span></div>
       <div class="sidebar-item" onclick="showRequests()"><i class="fas fa-bars"></i><span>{{ __('messages.requests') }}</span></div>
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
                           <form action="{{ route('manager.news.destroy', $news->id) }}"
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
   <!-- СЕКЦИЯ СОЗДАНИЯ НОВОСТИ (скрыта по умолчанию) -->
   <div class="main-content" id="create-news-section" style="display: none;">
       <div class="container">
           <h2>{{ __('messages.create_news') }}</h2>
           <div class="create-news-section">
               <form action="{{ route('manager.news.store') }}" method="POST" enctype="multipart/form-data">
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
   <!-- СЕКЦИЯ РЕДАКТИРОВАНИЯ НОВОСТИ (скрыта) -->
   <div class="main-content" id="edit-news-section" style="display: none;">
       <div class="container">
           <h2>{{ __('messages.edit_news') }}</h2>
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
   <!-- ===== SECTION: Личные данные ===== -->
   <div class="main-content" id="personal-section">
       <div class="container">
           <h2>{{ __('messages.personal_data') }}</h2>
           <div class="personal-card">
               <div class="personal-content">
                   <div class="personal-photo">
                       @if(Auth::user()->photo)
                           <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="{{ __('messages.user_photo') }}">
                       @else
                           <img src="https://via.placeholder.com/180x180?text=No+Photo" alt="{{ __('messages.user_photo') }}">
                       @endif
                   </div>
                   <div class="personal-info">
                       <div class="personal-name">{{ Auth::user()->name }}</div>
                       <div class="personal-status">{{ Auth::user()->job_type ?? '' }}</div>

                       <form class="personal-form" action="{{ route('manager.updateProfile') }}" method="POST" enctype="multipart/form-data">
                           @csrf
                           <div>
                               <label>{{ __('messages.email') }}</label>
                               <input type="email" value="{{ Auth::user()->email }}" disabled>
                           </div>
                           <div>
                               <label>{{ __('messages.phone_number') }}</label>
                               <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}">
                           </div>
                           <div>
                               <label>{{ __('messages.photo') }}</label>
                               <input type="file" name="photo">
                           </div>
                           <div>
                               <label>{{ __('messages.password') }}</label>
                               <div style="display: flex; gap: 10px;">
                                   <input type="password" value="********" disabled>
                                   <button type="button" class="btn btn-secondary" onclick="openPasswordModal()">
                                       {{ __('messages.change') }}
                                   </button>
                               </div>
                           </div>

                           <div class="personal-actions">
                               <button type="submit" class="btn btn-success">{{ __('messages.save') }}</button>
                           </div>
                       </form>
                   </div>
               </div>
           </div>
       </div>
   </div>
   <!-- ========== Секция пользователей (скрыта) ========== -->
   <div class="main-content" id="users-section" style="display: none;">
       <!-- Фильтр / поиск -->
       <form method="GET" action="{{ route('manager.users.index') }}" class="user-filter">
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
       <div class="user-details-card">
           <!-- Фото -->
           <div class="user-photo" id="user-photo">
               <img src="https://via.placeholder.com/180x180?text=No+Photo" alt="{{ __('messages.user_photo') }}">
           </div>
           <!-- Информация -->
           <div class="user-info">
               <h2 id="detail-name">{{ __('messages.full_name') }}</h2>
               <div class="status">{{ __('messages.status') }}: <span id="detail-role"></span></div>
               <div class="personal-location" id="user-location">
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

               <!-- Кнопки действий -->
               <div class="user-actions mt-3" id="userActions" style="display: none;">
                   <!-- Кнопка "Удалить/Выселить студента" -->
                   <form id="userDeleteForm" action="" method="POST" style="display: inline;">
                       @csrf
                       @method('DELETE')
                       <button type="submit" class="btn-danger" id="deleteButton">
                           {{ __('messages.expel_student') }}
                       </button>
                   </form>
                   <!-- Кнопка "Документы" -->
                   <button type="button" class="btn-primary" id="documentsButton" onclick="showDocuments()">
                       {{ __('messages.documents') }}
                   </button>
               </div>

           </div>
       </div>
   </div>
    <!-- Добавьте модальное окно для документов -->
    <div id="documentsModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeDocumentsModal()">&times;</span>
            <h3>{{ __('messages.documents') }}</h3>
            <div id="documents-list">
                <!-- Документы будут добавлены через JavaScript -->
            </div>
        </div>
    </div>

    <!-- СЕКЦИЯ заявки на заселение -->
   <div class="main-content" id="request-section" style="display: none;">
       <h2>{{ __('messages.requests') }}</h2>
       @if(session('success'))
           <div style="background-color: #d4edda; color: #155724; padding: 10px;">
               {{ session('success') }}
           </div>
       @endif
       <table style="width:100%; border-collapse: collapse;">
           <thead>
           <tr>
               <th>{{ __('messages.student') }}</th>
               <th>{{ __('messages.building') }}</th>
               <th>{{ __('messages.floor') }}</th>
               <th>{{ __('messages.room') }}</th>
               <th>{{ __('messages.status') }}</th>
               <th>{{ __('messages.actions') }}</th>
           </tr>
           </thead>
           <tbody>
           @foreach($requests as $req)
               <tr style="border-bottom: 1px solid #ccc;">
                   <td>{{ $req->user->name }}</td>
                   <td>{{ $req->building->name}}</td>
                   <td>{{ $req->floor }}</td>
                   <td>{{ $req->room->room_number }}</td>
                   <td>{{ $req->status }}</td>
                   <td>
                       <a href="{{ route('booking.accept', $req->id) }}"
                          style="color: green; text-decoration: none; margin-right: 10px;">
                           {{ __('messages.accept') }}
                       </a>
                       <a href="{{ route('booking.reject', $req->id) }}"
                          style="color: red; text-decoration: none;">
                           {{ __('messages.reject') }}
                       </a>
                   </td>
               </tr>
           @endforeach
           </tbody>
       </table>
   </div>

   <script>
        document.addEventListener('DOMContentLoaded', function() {
            showNews();
            @if($errors->any())
            openModal();
            @endif
            @if(session('successType') === 'news_created')
            seeNews();
            @elseif(session('successType') === 'news_updated')
            seeNews();
            @elseif(session('successType') === 'news_deleted')
            seeNews();
            @elseif(session('successType') === 'user_searched')
            showUsers();
            @elseif(session('successType') === 'request_accepted')
            showRequests();
            @endif
        });

        const deleteForm = document.getElementById('userDeleteForm');
        if (data.role === 'student' && '{{ auth()->user()->isAdmin() }}' === '1') {
            deleteForm.style.display = 'block';
            document.getElementById('deleteButton').textContent = '{{ __("messages.expel_student") }}';
        } else {
            deleteForm.style.display = 'none';
        }
        function showPersonal() {
            hideAllSections();
            document.getElementById('personal-section').style.display = 'block';
        }
        function showUsers() {
            hideAllSections();
            document.getElementById('users-section').style.display = 'block';
        }
        async function viewUserDetail(userId) {
            hideAllSections();
            let url = '/manager/users/' + userId + '/json';
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
                const locationElement = document.getElementById('user-location');
                if (data.role === 'student') {
                    if (data.accepted_booking) {
                        locationElement.innerHTML = `
                    <p><strong>{{ __('messages.building') }}:</strong> ${data.accepted_booking.building.name}</p>
                    <p><strong>{{ __('messages.address') }}:</strong> ${data.accepted_booking.building.address}</p>
                    <p><strong>{{ __('messages.floor') }}:</strong> ${data.accepted_booking.room.floor}</p>
                    <p><strong>{{ __('messages.room') }}:</strong> ${data.accepted_booking.room.room_number}</p>
                `;
                    } else {
                        locationElement.innerHTML = `<p>{{ __('messages.not_settled') }}</p>`;
                    }
                }
                document.getElementById('userUpdateForm').action = '/manager/dashboard/users/' + data.id;
                document.getElementById('userDeleteForm').action = '/manager/dashboard/users/' + data.id;
                const userActions = document.getElementById('userActions');
                const deleteForm = document.getElementById('userDeleteForm');
                const documentsButton = document.getElementById('documentsButton');
                userActions.style.display = 'none';
                deleteForm.style.display = 'none';
                documentsButton.style.display = 'none';

                if ((data.role === 'student') ) {
                    userActions.style.display = 'block';
                    if ({{ auth()->user()->isAdmin() ? 'true' : 'false' }}) {
                        deleteForm.style.display = 'inline';
                    }
                    documentsButton.style.display = 'inline';

                }
                document.getElementById('user-details-section').style.display = 'block';
            } catch (error) {
                console.error('Ошибка:', error);
                alert(error.message);
                showUsers();
            }
        }
        function openModal() {
            document.getElementById('modalOverlay').style.display = 'flex';
        }
        function closeModal() {
            document.getElementById('modalOverlay').style.display = 'none';
        }
        function seeNews() {
            hideAllSections()
            document.getElementById('news-section').style.display = 'block';
        }
        function CreateNews() {
            hideAllSections()
            document.getElementById('create-news-section').style.display = 'block';
        }
        function EditNews(id, title, content) {
            hideAllSections();
            document.getElementById('edit-news-section').style.display = 'block';
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-content').value = content;
            document.getElementById('editNewsForm').action = '/manager/dashboard/news/' + id;
        }
        function showRequests() {
            hideAllSections();
            document.getElementById('request-section').style.display = 'block';
        }
        function cancelEdit() {
            document.getElementById('edit-news-section').style.display = 'none';
            showNews();
        }
        function hideAllSections() {
            document.getElementById('news-section').style.display = 'none';
            document.getElementById('users-section').style.display = 'none';
            document.getElementById('user-details-section').style.display = 'none';
            document.getElementById('see-news-section').style.display = 'none';
            document.getElementById('create-news-section').style.display = 'none';
            document.getElementById('personal-section').style.display = 'none';
            document.getElementById('edit-news-section').style.display = 'none';
            document.getElementById('request-section').style.display = 'none';
        }
        function showDocuments() {
            const userId = document.getElementById('detail-user_id').value;
            fetchDocuments(userId);
            document.getElementById('documentsModal').style.display = 'flex';
        }

        function closeDocumentsModal() {
            document.getElementById('documentsModal').style.display = 'none';
        }

        async function fetchDocuments(userId) {
            try {
                const response = await fetch(`/manager/dashboard/users/${userId}/documents`);
                if (!response.ok) {
                    throw new Error('Ошибка при загрузке документов');
                }
                const documents = await response.json();

                const documentsList = document.getElementById('documents-list');
                documentsList.innerHTML = '';

                if (documents.length === 0) {
                    documentsList.innerHTML = '<p>У студента нет загруженных документов</p>';
                    return;
                }

                documents.forEach(doc => {
                    const item = document.createElement('div');
                    item.className = 'documents-list-item';
                    item.innerHTML = `
                <i class="fas fa-file document-icon"></i>
                <span class="document-name">${doc.file_name}</span>
                <span class="document-date">${new Date(doc.created_at).toLocaleDateString('ru-RU')}</span>
            `;
                    item.onclick = () => downloadDocument(doc.id);
                    documentsList.appendChild(item);
                });
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при загрузке документов');
            }
        }
        function downloadDocument(documentId) {
            window.location.href = `/manager/dashboard/documents/${documentId}/download`;
        }
        window.onclick = function(event) {
            const modal = document.getElementById('documentsModal');
            if (event.target === modal) {
                closeDocumentsModal();
            }
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDocumentsModal();
            }
        });
    </script>
@endsection
