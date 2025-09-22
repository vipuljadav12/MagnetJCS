@extends('layouts.admin.app')
@section('content')
<div class="card shadow">
                        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
                            <div class="page-title mt-5 mb-5">Dashboard</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
                            <div class="border shadow p-20 text-center">
                                <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="far fa-address-card"></i></div>
                                <div class="font-16 font-weight-bold">Submissions</div>
                                <hr class="border-3 max-width-100">
                                <div class="font-24 font-weight-bold">1650</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
                            <div class="border shadow p-20 text-center">
                                <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="fas fa-school"></i></div>
                                <div class="font-16 font-weight-bold">Schools</div>
                                <hr class="border-3 max-width-100">
                                <div class="font-24 font-weight-bold">20</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
                            <div class="border shadow p-20 text-center">
                                <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="far fa-star"></i></div>
                                <div class="font-16 font-weight-bold">Programs</div>
                                <hr class="border-3 max-width-100">
                                <div class="font-24 font-weight-bold">50</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
                            <div class="border shadow p-20 text-center">
                                <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="far fa-list-alt"></i></div>
                                <div class="font-16 font-weight-bold">Forms</div>
                                <hr class="border-3 max-width-100">
                                <div class="font-24 font-weight-bold">3</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
                            <div class="border shadow p-20 text-center">
                                <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="far fa-envelope"></i></div>
                                <div class="font-16 font-weight-bold">Emails</div>
                                <hr class="border-3 max-width-100">
                                <div class="font-24 font-weight-bold">10</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
                            <div class="border shadow p-20 text-center">
                                <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="far fa-file-alt"></i></div>
                                <div class="font-16 font-weight-bold">Letters</div>
                                <hr class="border-3 max-width-100">
                                <div class="font-24 font-weight-bold">4</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
                            <div class="border shadow p-20 text-center">
                                <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="fas fa-language"></i></div>
                                <div class="font-16 font-weight-bold">Translations</div>
                                <hr class="border-3 max-width-100">
                                <div class="font-24 font-weight-bold">110</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
                            <div class="border shadow p-20 text-center">
                                <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="far fa-user"></i></div>
                                <div class="font-16 font-weight-bold">Users</div>
                                <hr class="border-3 max-width-100">
                                <div class="font-24 font-weight-bold">60</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-20">
                            <div class="card shadow h-100 mb-0">
                                <div class="card-header">Submission Status</div>
                                <div class="card-body">
                                    <canvas id="myChart1" width="100%" height="70"></canvas>
                                    <div class="custom-legends" id="js-legend1"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-20">
                            <div class="card shadow h-100 mb-0">
                                <div class="card-header">Projected Population</div>
                                <div class="card-body">
                                    <canvas id="myChart2" width="100%" height="70"></canvas>
                                    <div class="custom-legends" id="js-legend2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th class="align-middle">Submission ID</th>                                                
                                                <th class="align-middle">Name</th>
                                                <th class="align-middle">Home Zone</th>
                                                <th class="align-middle">Next Grade</th>
                                                <th class="align-middle">Awarded School</th>
                                                <th class="align-middle text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="">2306</td>
                                                <td class="">LaDarian Page</td>
                                                <td class=""></td>
                                                <td class="">Grade 6</td>
                                                <td class=""><span class="small p-1 bg-success text-white">1st choice</span> The Alberta School of Performing Arts</td>
                                                <td class="">Offered and Accepted</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
@endsection