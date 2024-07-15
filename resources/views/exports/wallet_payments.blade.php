<!DOCTYPE html>
<html>

<head>
    <title>{{tr('wallet_payments')}}</title>
</head>
<style type="text/css">

    table{
        font-family: arial, sans-serif;
        border-collapse: collapse;
    }

    .first_row_design{
        background-color: #187d7d;
        color: #ffffff;
    }

    .row_col_design{
        background-color: #cccccc;
    }

    th{
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        font-weight: bold;

    }

    td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;

    }
    
</style>

<body>

    <table>

        <!-- HEADER START  -->

        <tr class="first_row_design">

            <th>{{tr('s_no')}}</th>

            <th>{{tr('payment_id')}}</th>

            <th>{{tr('from_user')}}</th>

            <th >{{tr('to_user')}}</th>

            <th >{{tr('paid_amount')}}</th>

            <th >{{tr('admin_amount')}}</th>

            <th >{{tr('user_amount')}}</th>

            <th >{{tr('amount_type')}}</th>

            <th >{{tr('payment_type')}}</th>

            <th >{{tr('payment_mode')}}</th>

            <th >{{tr('status')}}</th>

        </tr>

        <!--- HEADER END  -->
        {{$i=0}}

        @foreach($data as $wallet_payments)

            @foreach($wallet_payments as $wallet_payment)

            {{$i=$i+1}}

            <tr>

                <td>{{$i}}</td>

                <td>{{$wallet_payment->payment_id ?: tr('n_a')}}</td>

                <td>{{$wallet_payment->ReceivedFromUser->name ?? tr('n_a')}}</td>

                <td>{{$wallet_payment->toUser->name ?? tr('n_a')}}</td>

                <td>{{$wallet_payment->paid_amount_formatted ?: 0.00 }}</td>

                <td>{{$wallet_payment->admin_amount_formatted ?: 0.00 }}</td>

                <td>{{$wallet_payment->user_amount_formatted ?: 0.00 }}</td>

                <td>{{$wallet_payment->amount_type ?: tr('n_a')}}</td>

                <td>{{$wallet_payment->payment_type ?: tr('n_a')}}</td>

                <td>{{$wallet_payment->payment_mode ?: tr('n_a')}}</td>

                <td>
                    @if($wallet_payment->status == PAID)

                        {{ tr('paid') }}

                    @else

                        {{ tr('not_paid') }}
                        
                    @endif
                </td>

            </tr>

            @endforeach
        
        @endforeach
    </table>

</body>

</html>