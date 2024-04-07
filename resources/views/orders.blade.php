@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Список заказов</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>№ Заказа</th>
                <th>Имя пользователя</th>
                <th>Телефон</th>
                <th>Сумма чека</th>
                <th>Дата</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($orders as $order)

            @php
            $data = json_decode($order->data, true);
            @endphp
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $data['user']['first_name'] }}</td>
                <td>{{ $order->phone }}</td>
                <td>{{ $order->total_amount }} грн.</td>
                <td>{{ $order->created_at }}</td>
                <td>
                    <select class="form-select" id="status_{{ $order->id }}">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>В ожидании</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>В обработке</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Завершено</option>
                    </select>
                </td>
                <td>
                    <button class="btn btn-primary more_btn" onclick="toggleDetails({{ $order->id }}); this.classList.toggle('opened');"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg></button>

                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <div id="details_{{ $order->id }}" style="display: none;">

                        <div class="row user_data">
                            <div class="col-12 col-md-4"><strong>Пользователь:</strong>
                                {{ $data['user']['first_name'] }}
                                @if (!empty($data['user']['surname']))
                                {{ $data['user']['surname'] }}
                                @endif
                                {{ $data['user']['last_name'] }}

                            </div>

                            <div class="col-12 col-md-4"><strong>E-mail:</strong> {{ $order->email }}</div>
                            <div class="col-12 col-md-4"><strong>Метод оплаты:</strong> {{ $data['payment_method'] }}</div>
                        </div>
                        <h5>Товары в заказе:</h5>
                        <ul class="product_list">

                            @foreach ($data['products'] as $product)
                            <li class="row">
                                <div class="col-12 col-md-1 ">{{ $loop->iteration }}</div>
                                <div class="col-12 col-md-2 product_image">
                                    @if (!empty($product['image']))
                                    <img src="{{ request()->getScheme() }}://{{ request()->getHttpHost() }}/images/{{ $product['image'] }}" alt="Product Image">
                                    @else
                                    <img src="{{ asset('images/system/placeholder.jpeg') }}" alt="Placeholder Image">
                                    @endif
                                </div>
                                <div class="col-12 col-md-4 product_name">{{ $product['name'] }}</div>
                                <div class="col-12 col-md-1">{{ $product['quantity'] }} шт.</div>
                                <div class="col-12 col-md-2 item_price">{{ $product['price'] }} грн</div>
                                <div class="col-12 col-md-2 total_price">{{ $product['price']*$product['quantity'] }} грн</div>
                            </li>

                            @endforeach
                        </ul>
                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    // Функция для обновления статуса заказа
    function updateOrderStatus(orderId) {
        var status = document.getElementById('status_' + orderId).value;

        // Отправка AJAX-запроса для обновления статуса заказа
        fetch('/orders/' + orderId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Добавляем CSRF-токен для защиты от CSRF-атак
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to update order status');
            }
            alert('Статус заказа ' + orderId + ' обновлен: ' + status);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при обновлении статуса заказа');
        });
    }

    document.querySelectorAll('.form-select').forEach(select => {
        select.addEventListener('change', function() {
            var orderId = this.id.split('_')[1]; // Получаем ID заказа из ID селекта
            updateOrderStatus(orderId); // Вызываем функцию обновления статуса заказа
        });
    });

</script>


<script>
    function toggleDetails(orderId) {
        var detailsElement = document.getElementById('details_' + orderId);
        // buttonElement.classList.toggle('opened');
        if (detailsElement.style.display === 'none') {
            detailsElement.style.display = 'block';
        } else {
            detailsElement.style.display = 'none';
        }
    }
</script>

@endpush