<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
    <style>
        *{
            margin:0;
            padding:0;
        }
        .receipt{
            padding:5px;
        }
        body{
            font-size: 12px;
        }
        td{
            padding: 5px 0;
        }
        .totals td{
            padding: 0 2px;
        }
        .totals{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .totals tr td:first-child {
            font-weight: bold;
        }
        .paidBox{
            font-size: 20px;border:2px solid; padding:5px 5px 2px 5px;
            transform: rotate(-20deg); margin-left: 50px;
        }

    </style>
</head>
{{--<body onload="window.print()">--}}
<body>
<div class="receipt">
    @php
    $subtotal=0
    @endphp
    <header style="text-align: center">
        <img style="width:80px;" src="{{json_decode($settings->icon)->icon}}" alt="">
        <h2 class="center">{{json_decode($settings->name)->en}}</h2>
        <p class="center">{{json_decode($settings->address)->en}} <br>
            Hotline: {{json_decode($settings->address)->hotline}} </p>
    </header>
    <hr style="margin:3px 0" />
    <section style="display: flex; justify-content: space-between">
        <p><b>Date: </b>{{date_format($data->updated_at, 'd-m-Y')}}</p>
        <p><b>Time: </b>{{date_format($data->updated_at, 'h:i A')}} </p>
    </section>
    <hr style="margin:3px 0" />
    <section>
        <div>
            <p><b>Patient Name: </b>{{ $data->patient->name }} </p>
            <p><b>Emergency Contact: </b>{{ $data->patient->phone }}</p>
        </div>
        <div>
            <p><b>Address: </b>{{ $data->patient->address ?? 'N/A' }}</p>
            <p style="display: flex; justify-content: space-between">
                <span>
                    <strong>Blood: </strong>{{ $data->patient->blood }}
                </span>
                <span>
                    <strong>Age:</strong> {{$data->patient->age }} Years
                </span>
                <span>
                    <strong>Sex: </strong>{{ $data->patient->gender ? 'Male' : 'Female' }}
                </span>
            </p>
        </div>
    </section>
    <hr>

    <section style="margin-top: 20px;">
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                <tr>
                    <th style="border-bottom: 1px dashed #999; border-top: 1px dashed #999; text-align: left;">Medicine Name</th>
                    <th style="border-bottom: 1px dashed #999; border-top: 1px dashed #999; text-align: center;">Price</th>
                    <th style="border-bottom: 1px dashed #999; border-top: 1px dashed #999; text-align: right;">Quantity</th>
                    <th style="border-bottom: 1px dashed #999; border-top: 1px dashed #999; text-align: right;">Subtotal</th>
                </tr>
                </thead>
                <tbody>
                    @foreach(json_decode($data->medicines) as $medicine)
                        <tr>
                            <td style="text-align: left">{{ $medicine->name }}</td>
                            <td style="text-align: center">{{ number_format($medicine->price, 2) }}</td>
                            <td style="text-align: right">{{ $medicine->qty }}</td>
                            @php
                                $subtotal += floatval($medicine->price) * floatval($medicine->qty);
                            @endphp
                            <td style="text-align: right">{{ number_format($subtotal, 2) }}</td>                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

    <section class="totals">
        <div>
            <h6 class="paidBox">PAID</h6>
        </div>
        <table>
            <tbody>
            <tr>
                <td>Subtotal:</td>
                <td>{{ number_format($subtotal,2) }}</td>
            </tr>
            <tr>
                <td>Discount: (-)</td>
                <td>
                        @if($data->discount_type && $data->discount)
                            @if($data->discount_type === 1)
                                {{number_format($data->discount,2)}}
                            @else
                                {{number_format( $subtotal * (.01 * $data->discount),2)}}
                            @endif
                        @else
                            0.00
                        @endif
                </td>
            </tr>
            <tr>
                <td>VAT: (+)</td>
                <td>{{ number_format($data->VAT,2) }} %</td>
            </tr>
            <tr>
                <td>Payable:</td>
                <td>{{ number_format($data->payable,2) }}</td>
            </tr>
            <tr>
                <td>Received:</td>
                <td>{{ number_format($data->received,2) }}</td>
            </tr>
            <tr>
                <td>Changes:</td>
                <td>{{ number_format($data->changes,2) }}</td>
            </tr>
            <tr>
                <td>Due:</td>
                <td>{{ number_format($data->payable - $data->received, 2) }}</td>
            </tr>
            </tbody>
        </table>
    </section>
</div>

</body>
</html>
