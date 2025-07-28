@extends('dashboard_page.main')

@section('content')
<div class="row">
    {{-- Card 1 --}}
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Today's Money</p>
                            <h5 class="font-weight-bolder">$53,000</h5>
                            <p class="mb-0">
                                <span class="text-success text-sm font-weight-bolder">+55%</span>
                                since yesterday
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                            <i class="ni ni-money-coins text-lg opacity-10"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2 --}}
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Today's Users</p>
                            <h5 class="font-weight-bolder">2,300</h5>
                            <p class="mb-0">
                                <span class="text-success text-sm font-weight-bolder">+3%</span>
                                since last week
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                            <i class="ni ni-world text-lg opacity-10"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3 --}}
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">New Clients</p>
                            <h5 class="font-weight-bolder">+3,462</h5>
                            <p class="mb-0">
                                <span class="text-danger text-sm font-weight-bolder">-2%</span>
                                since last quarter
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                            <i class="ni ni-paper-diploma text-lg opacity-10"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4 --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Sales</p>
                            <h5 class="font-weight-bolder">$103,430</h5>
                            <p class="mb-0">
                                <span class="text-success text-sm font-weight-bolder">+5%</span> than last month
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                            <i class="ni ni-cart text-lg opacity-10"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sales by Country --}}
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-2">Sales by Country</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center">
                    <tbody>
                        @php
                            $countries = [
                                ['flag' => 'US', 'name' => 'United States', 'sales' => 2500, 'value' => '$230,900', 'bounce' => '29.9%'],
                                ['flag' => 'DE', 'name' => 'Germany', 'sales' => 3900, 'value' => '$440,000', 'bounce' => '40.22%'],
                                ['flag' => 'GB', 'name' => 'Great Britain', 'sales' => 1400, 'value' => '$190,700', 'bounce' => '23.44%'],
                                ['flag' => 'BR', 'name' => 'Brasil', 'sales' => 562, 'value' => '$143,960', 'bounce' => '32.14%'],
                            ];
                        @endphp
                        @foreach ($countries as $country)
                        <tr>
                            <td class="w-30">
                                <div class="d-flex px-2 py-1 align-items-center">
                                    <div><img src="../assets/img/icons/flags/{{ $country['flag'] }}.png" alt="Country flag"></div>
                                    <div class="ms-4">
                                        <p class="text-xs font-weight-bold mb-0">Country:</p>
                                        <h6 class="text-sm mb-0">{{ $country['name'] }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <p class="text-xs font-weight-bold mb-0">Sales:</p>
                                <h6 class="text-sm mb-0">{{ $country['sales'] }}</h6>
                            </td>
                            <td class="text-center">
                                <p class="text-xs font-weight-bold mb-0">Value:</p>
                                <h6 class="text-sm mb-0">{{ $country['value'] }}</h6>
                            </td>
                            <td class="align-middle text-sm text-center">
                                <p class="text-xs font-weight-bold mb-0">Bounce:</p>
                                <h6 class="text-sm mb-0">{{ $country['bounce'] }}</h6>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
