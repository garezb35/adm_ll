<table class="display table table-bordered table-nowrap" id="data-tables">
    <thead>
    <tr>
        <th>@lang('translation.date')</th>
        <th>@lang('translation.charge-amount')</th>
        <th>@lang('translation.excharge-amount')</th>
        <th>@lang('translation.chexloss')</th>
        <th>@lang('translation.betting-amount')</th>
        <th>@lang('translation.winner-amount')</th>
        <th>@lang('translation.rolling-amount')</th>
        <th>@lang('translation.betting-profit')</th>
        <th>@lang('translation.having-amount')</th>
        <th>@lang('translation.having-rolling-amount')</th>
    </tr>
    </thead>
    <tbody>
    <tr class="table-info">
        <td>@lang('translation.sum')</td>
        <td>{{number_format(array_sum(array_column($dateRecord, 'charge')))}}</td>
        <td>{{number_format(array_sum(array_column($dateRecord, 'excharge')))}}</td>
        <td>{!! printColorMoney(array_sum(array_column($dateRecord, 'loss1'))) !!}</td>
        <td>{{number_format(array_sum(array_column($dateRecord, 'bet')))}}</td>
        <td>{{number_format(array_sum(array_column($dateRecord, 'win')))}}</td>
        <td>{{number_format(array_sum(array_column($dateRecord, 'fee')))}}</td>
        <td>{!! printColorMoney(array_sum(array_column($dateRecord, 'loss2'))) !!}</td>
        <td></td>
        <td></td>
        {{--        <td>{{number_format(array_sum(array_column($dateRecord, 'money')))}}</td>--}}
        {{--        <td>{{number_format(array_sum(array_column($dateRecord, 'point')))}}</td>--}}
    </tr>
    @foreach ($dateRecord as $record)
        <tr>
            <td style="text-align: center;">{{$record['date']}}</td>
            <td>{{number_format($record['charge'])}}</td>
            <td>{{number_format($record['excharge'])}}</td>
            <td>{!! printColorMoney($record['loss1']) !!}</td>
            <td>{{number_format($record['bet'])}}</td>
            <td>{{number_format($record['win'])}}</td>
            <td>{{number_format($record['fee'])}}</td>
            <td>{!! printColorMoney($record['loss2']) !!}</td>
            <td>{{number_format($record['money'])}}</td>
            <td>{{number_format($record['point'])}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
