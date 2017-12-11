<div id="conversionTable">
    <table class="table table-striped">
        <thead>
        <tr>
            <td></td>
            <td class="text-uppercase">{{ $currencyFrom->shortName }}</td>
            <td class="text-uppercase">{{ $currencyTo->shortName }}</td>
            <td class="text-uppercase">{{ $currencyFrom->shortName }}</td>
            <td class="text-uppercase">{{ $currencyTo->shortName }}</td>
            <td></td>
        </tr>
        </thead>
        @isset($conversionTable)
            @foreach ($conversionTable as $item)
                <tr>
                    <td></td>
                    <td class="text-uppercase">{{ $item[0] }} {{ $currencyFrom->shortName }} =</td>
                    <td class="text-uppercase">{{ $item[1] }} {{ $currencyTo->shortName }}</td>

                    <td class="text-uppercase">{{ $item[2] }} {{ $currencyFrom->shortName }} =</td>
                    <td class="text-uppercase">{{ $item[0] }} {{ $currencyTo->shortName }} </td>
                    <td></td>
                </tr>
            @endforeach
        @endisset
    </table>
</div>

