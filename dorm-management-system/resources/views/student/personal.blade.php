
@extends('layouts.app')
@section('content')
    <link href="{{ asset('css/studentPersonal.css') }}" rel="stylesheet">
{{--     ЛЕВАЯ ПАНЕЛЬ--}}
    <div class="sidebar">
        <div class="sidebar-item" onclick="showNews()">
            <i class="fas fa-home"></i>
            <span>{{__('messages.main')}}</span>
        </div>
        <div class="sidebar-item" onclick="showPersonal()">
            <i class="fas fa-user"></i>
            <span>{{__('messages.personal_information')}}</span>
        </div>
        @if(Auth::user()->student && Auth::user()->student->room_id)
            <div class="sidebar-item" onclick="showHousing()">
                <i class="fas fa-building"></i>
                <span>{{__('messages.accommodation')}}</span>
            </div>
            <div class="sidebar-item" onclick="showDocuments()">
                <i class="fa-solid fa-clipboard"></i>
                <span>{{__('messages.documents')}}</span>
            </div>
            <div class="sidebar-item" onclick="showFinance()">
                <i class="fas fa-wallet"></i>
                <span>{{__('messages.financial_cabinet')}}</span>
            </div>
            <div class="sidebar-item" onclick = "showRequestRepair()">
                <i class="fas fa-wrench"></i>
                <span>{{__('messages.repair_requests')}}</span>
            </div>
        @endif
        <div class="sidebar-item" onclick="showSportsBooking()">
            <i class="fas fa-dumbbell"></i>
            <span>{{__('messages.registration_for_physical_edu')}}</span>
        </div>
    </div>
    <!-- Личная информация -->
    <div class="main-content" id="personal-section" style="display: none;">
        <h2>{{ __('messages.personal_data') }}</h2>
        <div class="personal-card">
            <div class="personal-content">
                <div class="personal-photo">
                    @if(Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="User Photo">
                    @else
                        <img src="https://via.placeholder.com/180x180?text=No+Photo" alt="User Photo">
                    @endif
                </div>
                <div class="personal-info">
                    <div class="personal-name">{{ Auth::user()->name }}</div>
                    <!-- Пример локации -->
                    <div class="personal-location">
                        @if(Auth::user()->acceptedBooking)
                            {{ __('messages.building') }}: {{ Auth::user()->acceptedBooking->building->name }}<br>
                            {{ __('messages.address') }}: {{ Auth::user()->acceptedBooking->building->address }}<br>
                            {{ __('messages.floor') }}: {{ Auth::user()->acceptedBooking->room->floor }}<br>
                            {{ __('messages.room') }}: {{ Auth::user()->acceptedBooking->room->room_number }}
                        @else
                            <p>{{ __('messages.not_settled') }}</p>
                        @endif
                    </div>

                    <!-- Форма для изменения телефона и фото -->
                    <form class="personal-form"
                          action="{{ route('student.profile.update') }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label>ID</label>
                            <input type="text" value="{{ Auth::user()->user_id }}" disabled>
                        </div>
                        <div>
                            <label>{{ __('messages.phone_number') }}</label>
                            <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}">
                        </div>
                        <div>
                            <label>{{ __('messages.email') }}</label>
                            <input type="email" value="{{ Auth::user()->email }}" disabled>
                        </div>
                        <div>
                            <label>{{ __('messages.password') }}</label>
                            <!-- Вместо реального пароля показываем звездочки -->
                            <div style="display: flex; gap: 10px;">
                                <input type="password" value="********" disabled>
                                <!-- Кнопка, открывающая модальное окно -->
                                <button type="button" class="btn-change" onclick="openPasswordModal()">
                                    {{ __('messages.change') }}
                                </button>
                            </div>
                        </div>
                        <div>
                            <label>{{ __('messages.photo') }}</label>
                            <input type="file" name="photo">
                        </div>
                        <div class="personal-actions">
                            <button type="submit" class="btn-change">{{ __('messages.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- МОДАЛЬНОЕ ОКНО ДЛЯ СМЕНЫ ПАРОЛЯ -->
    <div class="modal-overlay" id="passwordModal">
        <div class="modal-content">
            <button class="close-button" onclick="closePasswordModal()">&times;</button>
            <h2>{{ __('messages.change_password') }}</h2>

            @if(session('password_success'))
                <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px;">
                    {{ session('password_success') }}
                </div>
            @endif

            <form action="{{ route('student.profile.update') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="current_password">{{ __('messages.current_password') }}</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">{{ __('messages.new_password') }}</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">{{ __('messages.repeat_new_password') }}</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>
                </div>
                <button type="submit" class="btn-change">{{ __('messages.update') }}</button>
            </form>
        </div>
    </div>
    <!-- Проживание -->
    <div class="main-content" id="housing-section">
        <h2>{{ __('messages.accommodation') }}</h2>
        <div class="housing-card">
            <h3>{{ __('messages.accommodation') }}</h3>
            @if(Auth::user()->acceptedBooking)
                <p class="personal-location">
                    {{ __('messages.building') }}: {{ Auth::user()->acceptedBooking->building->name }},
                    {{ __('messages.address') }}: {{ Auth::user()->acceptedBooking->building->address }},
                    {{ __('messages.floor') }}: {{ Auth::user()->acceptedBooking->room->floor }},
                    {{ __('messages.room') }}: {{ Auth::user()->acceptedBooking->room->room_number }}
                </p>
            @else
                <p>{{ __('messages.not_yet_settled') }}</p>
            @endif
            <button class="btn-finance" onclick="openChangeRoomModal()">{{ __('messages.change_room') }}</button>
            <a href="{{ route('refresh.user') }}" class="btn btn-secondary">{{ __('messages.refresh_data') }}</a>
        </div>
        <div class="housing-card">
            <h3>{{ __('messages.upcoming_payments') }}</h3>
            <button class="btn-finance" onclick="showFinance()">{{ __('messages.check_financial_cabinet') }}</button>
        </div>
    </div>
    <!-- Модальное окно для смены комнаты -->
    <div class="modal-overlay" id="changeRoomModal" style="display: none;">
        <div class="modal-content">
            <button class="close-button" onclick="closeChangeRoomModal()">&times;</button>
            <h2>{{ __('messages.room_change_request') }}</h2>

            <!-- Форма для смены комнаты -->
            <form action="{{ route('booking.changeRoom') }}" method="POST">
                @csrf
                <label for="buildingSelect">{{ __('messages.building') }}:</label>
                <select id="buildingSelect" name="building_id">
                    <option value="">{{ __('messages.select_building') }}</option>
                    @foreach($buildings as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>

                <label for="floorSelect">{{ __('messages.floor') }}:</label>
                <select id="floorSelect" name="floor" disabled>
                    <option value="">{{ __('messages.select_building_first') }}</option>
                </select>

                <label for="roomSelect">{{ __('messages.room') }}:</label>
                <select id="roomSelect" name="room_id" disabled>
                    <option value="">{{ __('messages.select_floor_first') }}</option>
                </select>

                <button type="submit" class="btn-change">{{ __('messages.submit_request') }}</button>
            </form>
        </div>
    </div>
    <!-- Документы -->
    <div id="documents-section" class="main-content" style="display: none;">
        <h2>{{ __('messages.documents') }}</h2>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.type') }}</th>
                    <th>{{ __('messages.file') }}</th>
                    <th>{{ __('messages.upload_date') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td>{{ $doc->id }}</td>
                        <td>{{ __('messages.' . $doc->type) }}</td>
                        <td>{{ $doc->file_name }}</td>
                        <td>{{ $doc->created_at->format('d.m.Y') }}</td>
                        <td>
                            <a href="{{ route('document.download', $doc->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i> {{ __('messages.download') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">{{ __('messages.no_documents_found') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <button id="uploadButton" class="btn btn-primary mt-3">{{ __('messages.upload_new') }}</button>
    </div>
    <!-- Форма загрузки документа (скрыта по умолчанию) -->
    <div id="uploadForm" style="display: none; margin-top: 20px;">
        <form action="{{ route('document.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-2">
                <label for="documentType" class="form-label">{{ __('messages.document_type') }}:</label>
                <select name="documentType" id="documentType" class="form-control" required>
                    <option value="">{{ __('messages.select_document_type') }}</option>
                    <option value="certificate">{{ __('messages.certificate') }}</option>
                    <option value="id">{{ __('messages.identity_document') }}</option>
                    <option value="quota">{{ __('messages.quota_document') }}</option>
                    <option value="other">{{ __('messages.other') }}</option>
                </select>
            </div>
            <div class="mb-2">
                <label for="documentFile" class="form-label">{{ __('messages.select_file') }}:</label>
                <input type="file" name="documentFile" id="documentFile" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">{{ __('messages.upload') }}</button>
            <button type="button" id="cancelUpload" class="btn btn-secondary">{{ __('messages.cancel') }}</button>
        </form>
    </div>
    <div id="financeSection" class="main-content" style="display: none;">
        <h2>{{ __('messages.finance_section_title') }}</h2>

        <div class="finance-dashboard">
            <!-- Карточка баланса -->
            <div class="finance-card balance-card">
                <h3>{{ __('messages.current_balance') }}</h3>
                <div class="balance-amount">{{ number_format($balance ?? 0, 0, ',', ' ') }} ₸</div>
                <p>{{ __('messages.last_updated') }}: {{ now()->format('d.m.Y H:i') }}</p>
            </div>
            <div class="finance-card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; ">
                <div class="qr-container" style="text-align: center; width: 100%; max-width: 300px;">
                    <h3 style="margin-bottom: 20px;">Оплата через Kaspi</h3>
                    <img src="{{ asset('storage/images/kaspi-qr.jpg') }}"
                         alt="Kaspi QR"
                         style="width: 70%; height: auto; border: 1px solid #ddd; border-radius: 4px; display: block; margin: 0 auto;">
                    <p style="margin-top: 15px; color: #666;">Отсканируйте QR-код для оплаты через Kaspi</p>
                </div>
            </div>

            <!-- Карточка оплаты -->
            <div class="finance-card">
                <h3>{{ __('messages.test_payment_title') }}</h3>
                <div class="alert alert-info">
                    {{ __('messages.test_mode_notice') }}
                </div>
                <form id="kaspiPaymentForm" class="mt-4">
                    @csrf
                    <div class="form-group">
                        <label for="amount">{{ __('messages.payment_amount_label') }}</label>
                        <input type="number"
                               id="amount"
                               name="amount"
                               min="100"
                               step="100"
                               required
                               class="form-control"
                               placeholder="{{ __('messages.payment_amount_label') }}">
                    </div>
                    <button type="submit" class="btn-finance mt-3">
                        <i class="fas fa-credit-card"></i> {{ __('messages.test_payment_button') }}
                    </button>
                </form>
            </div>

            <!-- История платежей -->
            <div class="finance-card payment-history-card">
                <h3>{{ __('messages.payment_history_title') }}</h3>
                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.amount') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.payment_method') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($payments ?? [] as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ number_format($payment->amount, 0, ',', ' ') }} ₸</td>
                            <td>
                            <span class="payment-status status-{{ $payment->status }}">
                                {{ __('messages.payment_status_' . $payment->status) }}
                            </span>
                            </td>
                            <td>{{ $payment->payment_method }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Запросы на ремонт -->
    <div class="flex space-x-6 items-start main-content" id="repair-list" style="display: none;">
        <div class="row">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title"><strong>{{ __('messages.create_repair_request') }}</strong></h2>
                        <p class="card-text">{{ __('messages.repair_description') }}</p>
                        <!-- Кнопка, вызывающая JS-функцию открытия модального окна -->
                        <button type="button" class="btn btn-primary mt-3" onclick="openRepairModal()">{{ __('messages.create_request') }}</button>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title"><strong>{{ __('messages.my_requests') }}</strong></h2>
                        <p class="card-text">{{ __('messages.view_requests_description') }}</p>
                        <!-- При клике показываем блок со списком запросов -->
                        <button type="button" class="btn btn-primary mt-3" onclick="openRequestList()">{{ __('messages.view_requests') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Модальное окно для создания запроса на ремонт -->
    <div id="repairModal" class="modal-overlay">
        <div class="modal-content">
            <!-- Кнопка закрытия модального окна -->
            <button class="close-button absolute top-2 right-2 text-xl" onclick="closeRepairModal()">&times;</button>
            <h2 class="text-lg font-semibold text-gray-800 text-center">{{ __('messages.new_repair_request') }}</h2>
            <form action="{{ route('request.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mt-4">
                    <label class="block text-sm text-gray-600">{{ __('messages.problem_type') }}</label>
                    <select name="type" id="type" class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option selected disabled>{{ __('messages.select_problem') }}</option>
                        <option value="Электрика">{{ __('messages.electricity') }}</option>
                        <option value="Водопровод">{{ __('messages.plumbing') }}</option>
                        <option value="Другое">{{ __('messages.other') }}</option>
                    </select>
                </div>

                <div class="mt-4">
                    <label class="block text-sm text-gray-600">{{ __('messages.problem_description') }}</label>
                    <textarea class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" rows="3" placeholder="{{ __('messages.enter_description') }}" name="description"></textarea>
                </div>

                <div class="mt-4">
                    <input type="file" id="file-upload" class="hidden" name="file">
                    <label for="file-upload" id="file-label" class="text-sm text-gray-500 cursor-pointer block border-dashed border-2 p-2 rounded-lg text-center">
                        📎 {{ __('messages.attach_file') }} ({{ __('messages.not_selected') }})
                    </label>
                </div>

                <div class="mt-4">
                    <label class="block text-sm text-gray-600">{{ __('messages.select_employee') }}</label>
                    <select class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" name="employee">
                        <option selected disabled>{{ __('messages.select_employee_problem') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach

                    </select>
                </div>

                <div class="flex justify-between mt-6">
                    <button type="submit" class="bg-green-300 text-gray-800 px-4 py-2 hover:bg-green-400 rounded">{{ __('messages.submit') }}</button>
                    <button type="button" onclick="closeRepairModal()" class="bg-gray-300 text-gray-800 px-4 py-2 hover:bg-gray-400 rounded">{{ __('messages.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Блок со списком запросов -->
    <div class="container mt-5 main-content" id="request-list" style="display: none;">
        <div class="card shadow-sm">
            <div class="card-body">
                <div style="grid-column: 1/-1; margin-bottom: 15px;">
                    <button class="btn btn-secondary" onclick="closeRequestList()" style="height: 32px; padding: 6px 12px; font-size: 13px;">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button type="button" class="btn btn-primary btn-sm" onclick="openRepairModal()">➕</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>№</th>
                            <th>{{ __('messages.request') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.employee') }}</th>
                            <th>{{ __('messages.status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>
                                    <!-- При клике передаём id запроса -->
                                    <a href="javascript:void(0)" class="request-link text-primary text-decoration-none"
                                       onclick="openRequestDetails({{ $request->id }})">
                                        {{ $request->type }}
                                    </a>
                                </td>
                                <td>{{ $request->created_at }}</td>
                                <td>{{ $request->employee->name ?? __('messages.not_assigned') }}</td>
                                <td>
                                    <span class="badge bg-success">{{__('status.' . $request->status)}}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Детали запросов-->
    @foreach($requests as $request)
        <div id="request-details-{{ $request->id }}" class="container mt-5 main-content" style="display: none;">
            <div class="container">

                <!-- Кнопки действий -->
                <div class="d-flex mb-3">
                    <button class="btn btn-secondary me-2" onclick="closeRequestDetails({{ $request->id }})">{{__('messages.back')}}</button>
                    <button type="button" class="btn btn-primary me-2" onclick="openEditModal({{ $request->id }})">{{__('messages.edit')}}</button>
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $request->id }}">
                        {{__('messages.delete')}}
                    </button>
                </div>

                <!-- Таблица с деталями запроса -->
                <table class="table table-bordered align-middle mb-4">
                    <thead class="table-light">
                    <tr>
                        <th>{{__('messages.number')}}</th>
                        <th>{{__('messages.request')}}</th>
                        <th>{{__('messages.details')}}</th>
                        <th>{{__('messages.date')}}</th>
                        <th>{{__('messages.employee')}}</th>
                        <th>{{__('messages.status')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->type }}</td>
                        <td>{{ $request->description }}</td>
                        <td>{{ $request->created_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $request->employee->name ?? __('messages.not_assigned') }}</td>
                        <td><span class="badge bg-success">{{ $request->status }}</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Модальное окно для подтверждения удаления запроса -->
            <div class="modal fade" id="deleteModal-{{ $request->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $request->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel-{{ $request->id }}">{{__('messages.delete_request')}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{__('messages.close')}}"></button>
                        </div>
                        <div class="modal-body">
                            {{__('messages.are_you_sure_delete')}}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                            <form action="{{ route('request.destroy', $request->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">{{__('messages.delete')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <!-- Модальное окно для редактирования запроса (скрыто по умолчанию) -->
    @foreach($requests as $request)
        <div class="modal-overlay" id="edit-modal-{{ $request->id }}" style="display: none; justify-content: center; align-items: center;">
            <div class="modal-content bg-white shadow-xl rounded-2xl p-6 w-96 relative">
                <button class="close-button absolute top-2 right-2 text-xl" onclick="closeEditModal({{ $request->id }})">&times;</button>
                <h2 class="mb-4 mt-5">{{__('messages.edit_request')}} #{{ $request->id }}</h2>
                <a href="javascript:void(0)" onclick="closeEditModal({{ $request->id }})" class="btn btn-secondary mb-3">{{__('messages.back')}}</a>
                <!-- Форма редактирования -->
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('request.update', $request->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <!-- Тип запроса -->
                            <div class="mb-3">
                                <label for="type-{{ $request->id }}" class="form-label">{{__('messages.request_type')}}</label>
                                <input type="text" id="type-{{ $request->id }}" name="type" class="form-control" value="{{ $request->type }}" required>
                            </div>
                            <!-- Описание -->
                            <div class="mb-3">
                                <label for="description-{{ $request->id }}" class="form-label">{{__('messages.description')}}</label>
                                <textarea id="description-{{ $request->id }}" name="description" class="form-control" rows="3" required>{{ $request->description }}</textarea>
                            </div>
                            <!-- Сотрудник -->
                            <div class="mb-3">
                                <label for="employee_id-{{ $request->id }}" class="form-label">{{__('messages.employee')}}</label>
                                <select id="employee_id-{{ $request->id }}" name="employee_id" class="form-control">
                                    <option value="">{{__('messages.not_assigned')}}</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $request->employee_id == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Кнопки действий -->
                            <button type="submit" class="btn btn-success">{{__('messages.save_changes')}}</button>
                            <button type="button" class="btn btn-danger" onclick="closeEditModal({{ $request->id }})">{{__('messages.cancel')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <!-- Запись на занятия физкультурой -->
    <div class="main-content" id="sports-section" style="display: none;">
        <h2>{{__('messages.sports_booking')}}</h2>
        @if($booking)
            <!-- ВАРИАНТ 2: Пользователь уже записан -->
            <div class="sports-result" id="sportsResultBlock">
                <h3>{{__('messages.already_booked')}}</h3>
                <div class="sports-info">
                    <div class="info-item">
                        <label>{{__('messages.sport_type')}}:</label>
                        <span>{{ $booking->sport }}</span>
                    </div>
                    <div class="info-item">
                        <label>{{__('messages.day_time')}}:</label>
                        <span>{{ $booking->day }} {{ $booking->scheduled_time }}</span>
                    </div>
                    <!-- Форма для отмены записи на занятие -->
                    <form action="{{ route('sports.cancel') }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-change">{{__('messages.cancel_booking')}}</button>
                    </form>
                </div>

                <!-- Блок отработки -->
                <div class="recovery-section">
                    <h4>{{__('messages.recovery_section')}}</h4>
                    @if($recoveries->count() > 0)
                        <div class="recovery-list">
                            @foreach($recoveries as $recovery)
                                <div class="recovery-item">
                                    <div class="info-item">
                                        <label>{{__('messages.sport_type')}}:</label>
                                        <span>{{ $recovery->sport }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>{{__('messages.time')}}:</label>
                                        <span>{{ $recovery->scheduled_time }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>{{__('messages.date')}}:</label>
                                        <span>{{ $recovery->created_at->format('d.m.Y') }}</span>
                                    </div>
                                    <!-- Форма для отмены отработки -->
                                    <form action="{{ route('sports.recovery.cancel', $recovery->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-change">{{__('messages.cancel_recovery')}}</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Форма добавления отработки (изначально скрыта) -->
                    <div class="sports-form" id="recovery-form" style="display: none;">
                        <form action="{{ route('sports.recovery') }}" method="POST">
                            @csrf
                            <label for="sport">{{__('messages.sport_type')}}</label>
                            <select name="sport" id="sport" required>
                                <option value="">{{__('messages.choose')}}</option>
                                <option value="Танцы">{{__('messages.dance')}}</option>
                                <option value="Баскетбол">{{__('messages.basketball')}}</option>
                                <option value="Волейбол">{{__('messages.volleyball')}}</option>
                                <option value="Футбол">{{__('messages.football')}}</option>
                            </select>

                            <!-- Блок выбора дней недели -->
                            <label>{{__('messages.select_days')}}</label>
                            <div id="day-selection">
                                <label><input type="checkbox" name="day[]" value="Понедельник">{{__('messages.monday')}}</label>
                                <label><input type="checkbox" name="day[]" value="Вторник">{{__('messages.tuesday')}}</label>
                                <label><input type="checkbox" name="day[]" value="Среда">{{__('messages.wednesday')}}</label>
                                <label><input type="checkbox" name="day[]" value="Четверг">{{__('messages.thursday')}}</label>
                                <label><input type="checkbox" name="day[]" value="Пятница">{{__('messages.friday')}}</label>
                                <label><input type="checkbox" name="day[]" value="Суббота">{{__('messages.saturday')}}</label>
                                <label><input type="checkbox" name="day[]" value="Воскресенье">{{__('messages.sunday')}}</label>
                            </div>

                            <label for="time">{{__('messages.select_time')}}</label>
                            <input type="time" name="time" id="time" required>

                            <div class="checkbox-group">
                                <input type="checkbox" id="autoEnroll">
                                <label for="autoEnroll">{{__('messages.auto_enroll')}}</label>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-primary">{{__('messages.save')}}</button>
                                <button type="button" class="btn-secondary" onclick="toggleRecoveryForm()">{{__('messages.cancel')}}</button>
                            </div>
                        </form>
                    </div>

                    <!-- Кнопка для показа формы отработки -->
                    <button onclick="toggleRecoveryForm()" class="btn-change" id="add-recovery-btn">+</button>
                </div>
            </div>
        @else
            <!-- ВАРИАНТ 1: Форма записи -->
            <div class="sports-form" id="sportsFormBlock">
                <h3>{{__('messages.sports_booking')}}</h3>
                <form id="sportsForm" action="{{ route('sports.store') }}" method="POST">
                    @csrf

                    <label for="sport">{{__('messages.sport_type')}}</label>
                    <select name="sport" id="sport" required>
                        <option value="">{{__('messages.choose')}}</option>
                        <option value="Танцы">{{__('messages.dance')}}</option>
                        <option value="Баскетбол">{{__('messages.basketball')}}</option>
                        <option value="Волейбол">{{__('messages.volleyball')}}</option>
                        <option value="Футбол">{{__('messages.football')}}</option>
                    </select>

                    <!-- Блок выбора дней недели -->
                    <label>{{__('messages.select_days')}}</label>
                    <div id="day-selection">
                        <label><input type="checkbox" name="day[]" value="Понедельник">{{__('messages.monday')}}</label>
                        <label><input type="checkbox" name="day[]" value="Вторник">{{__('messages.tuesday')}}</label>
                        <label><input type="checkbox" name="day[]" value="Среда">{{__('messages.wednesday')}}</label>
                        <label><input type="checkbox" name="day[]" value="Четверг">{{__('messages.thursday')}}</label>
                        <label><input type="checkbox" name="day[]" value="Пятница">{{__('messages.friday')}}</label>
                        <label><input type="checkbox" name="day[]" value="Суббота">{{__('messages.saturday')}}</label>
                        <label><input type="checkbox" name="day[]" value="Воскресенье">{{__('messages.sunday')}}</label>
                    </div>

                    <label for="time">{{__('messages.select_time')}}</label>
                    <input type="time" name="time" id="time" required>

                    <div class="checkbox-group">
                        <input type="checkbox" id="autoEnroll">
                        <label for="autoEnroll">{{__('messages.auto_enroll')}}</label>
                    </div>

                    <button type="submit" class="btn-primary">{{__('messages.book_now')}}</button>
                    <button type="button" class="btn-secondary" onclick="cancelSportsForm()">{{__('messages.cancel')}}</button>
                </form>
            </div>
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showNews();
            @if(session('successType') === 'profile_updated')
            showPersonal();
            @elseif(session('successType') === 'request_updated')
            openRequestList();
            @elseif(session('successType') === 'request_created')
            openRequestList();
            @elseif(session('successType') === 'gym_created')
            showSportsBooking();
            @elseif(session('successType') === 'gym_canceled')
            showSportsBooking();
            @elseif(session('successType') === 'recovery_created')
            showSportsBooking();
            @elseif(session('successType') === 'recovery_canceled')
            showSportsBooking();
            @elseif(session('successType') === 'change_room_created')
            showHousing();
            @elseif(session('successType') === 'user_updated')
            showHousing()
            @elseif(session('successType') === 'document_uploaded')
            showDocuments();
            @elseif(session('successType') === 'payment_success')
            showFinance();
            @endif
        });
        function toggleRecoveryForm() {
            const form = document.getElementById('recovery-form');
            const btn = document.getElementById('add-recovery-btn');
            if (form.style.display === 'none') {
                form.style.display = 'block';
                btn.style.display = 'none';
            } else {
                form.style.display = 'none';
                btn.style.display = 'block';
            }
        }

        // При загрузке страницы убедимся, что форма скрыта
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('recovery-form');
            if (form) {
                form.style.display = 'none';
            }
        });
        function showPersonal() {
            hideAllSections()
            document.getElementById('personal-section').style.display = 'block';
        }
        function openPasswordModal() {
            document.getElementById('passwordModal').style.display = 'flex';
        }
        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }
        function showHousing() {
            hideAllSections()
            document.getElementById('housing-section').style.display = 'block';
        }
        function openChangeRoomModal() {
            document.getElementById('changeRoomModal').style.display = 'flex';
        }
        function closeChangeRoomModal() {
            document.getElementById('changeRoomModal').style.display = 'none';
        }
        document.addEventListener('DOMContentLoaded', function() {
            const buildingSelect = document.getElementById('buildingSelect');
            const floorSelect = document.getElementById('floorSelect');
            const roomSelect = document.getElementById('roomSelect');

            buildingSelect.addEventListener('change', async function() {
                const buildingId = this.value;
                floorSelect.disabled = true;
                roomSelect.disabled = true;
                floorSelect.innerHTML = '<option>Загрузка...</option>';
                roomSelect.innerHTML = '<option>Сначала выберите этаж</option>';

                if (!buildingId) return;

                const response = await fetch(`/student/personal/floors/${buildingId}`);
                const floors = await response.json();

                if (!floors || floors.length === 0) {
                    floorSelect.innerHTML = '<option>Нет доступных этажей</option>';
                    return;
                }

                floorSelect.innerHTML = '<option value="">Выберите этаж</option>';
                floors.forEach(f => {
                    floorSelect.innerHTML += `<option value="${f}">${f}</option>`;
                });
                floorSelect.disabled = false;
            });

            floorSelect.addEventListener('change', async function() {
                const buildingId = buildingSelect.value;
                const floor = this.value;
                roomSelect.disabled = true;
                roomSelect.innerHTML = '<option>Загрузка...</option>';

                if (!floor) return;

                const response = await fetch(`/student/personal/rooms/${buildingId}/${floor}`);
                const rooms = await response.json();

                if (!rooms || rooms.length === 0) {
                    roomSelect.innerHTML = '<option>Нет доступных комнат</option>';
                    return;
                }

                roomSelect.innerHTML = '<option value="">Выберите комнату</option>';
                rooms.forEach(r => {
                    roomSelect.innerHTML += `<option value="${r.id}">${r.room_number}</option>`;
                });
                roomSelect.disabled = false;
            });
            console.log("DOM полностью загружен");
        });
        function showDocuments() {
            hideAllSections();
            document.getElementById('documents-section').style.display = 'block';
        }

        // Добавьте функцию показа финансового раздела
        function showFinance() {
            hideAllSections();
            document.getElementById('financeSection').style.display = 'block';
        }

        document.getElementById('kaspiPaymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            try {
                const response = await fetch('/student/payment/initiate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        amount: document.getElementById('amount').value
                    })
                });

                const result = await response.json();

                if (result.success && result.payment_url) {
                    window.location.href = result.payment_url;
                } else {
                    alert(result.message || 'Ошибка при создании платежа');
                }
            } catch (error) {
                console.error('Payment error:', error);
                alert('Произошла ошибка при обработке платежа');
            }
        });

        document.getElementById('uploadButton').addEventListener('click', function () {
            document.getElementById('uploadForm').style.display = 'block';
        });
        document.getElementById('cancelUpload').addEventListener('click', function () {
            document.getElementById('uploadForm').style.display = 'none';
        });
        function showRequestRepair() {
            hideAllSections();
            document.getElementById('repair-list').style.display = 'block';
        }
        function openRequestDetails(id) {
            hideAllSections();
            document.getElementById('request-details-' + id).style.display = 'block';
        }
        function closeRequestDetails(id) {
            document.getElementById('request-details-' + id).style.display = 'none';
            document.getElementById('request-list').style.display = 'block';
        }
        function openRequestList() {
            hideAllSections();
            document.getElementById('request-list').style.display = 'block';
        }
        function closeRequestList() {
            document.getElementById('request-list').style.display = 'none';
            document.getElementById('repair-list').style.display = 'block';
        }
        function openRepairModal() {
            document.getElementById('repairModal').style.display = 'flex';
        }
        function closeRepairModal() {
            document.getElementById('repairModal').style.display = 'none';
        }
        function openEditModal(id) {
            document.getElementById('edit-modal-' + id).style.display = 'flex';
        }
        function closeEditModal(id) {
            document.getElementById('edit-modal-' + id).style.display = 'none';
        }
        document.getElementById("file-upload").addEventListener("change", function () {
            let fileName = this.files[0] ? this.files[0].name : "Не выбрано";
            document.getElementById("file-label").textContent = `📎 ${fileName}`;
        });
        function showSportsBooking() {
            hideAllSections();
            document.getElementById('sports-section').style.display = 'block';
        }
        function cancelSportsForm() {
            document.getElementById('sport').value = '';
            document.getElementById('time').value = '';
            const checkboxes = document.querySelectorAll('#day-selection input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = false);
        }
        function showRecoveryModal() {
            document.getElementById('recoveryModal').style.display = 'flex';
        }
        function closeRecoveryModal() {
            document.getElementById('recoveryModal').style.display = 'none';
        }
        function hideAllSections() {
            document.getElementById('see-news-section').style.display = 'none';
            document.getElementById('personal-section').style.display = 'none';
            document.getElementById('housing-section').style.display = 'none';
            document.getElementById('documents-section').style.display='none';
            document.getElementById('financeSection').style.display = 'none';
            document.getElementById('repair-list').style.display = 'none';
            document.getElementById('request-list').style.display = 'none';
            document.getElementById('repairModal').style.display = 'none';
            document.getElementById('sports-section').style.display = 'none';
            @foreach($requests as $request)
            document.getElementById('edit-modal-{{ $request->id }}').style.display = 'none';
            document.getElementById('request-details-{{ $request->id }}').style.display = 'none';
            @endforeach
        }
    </script>
@endsection
