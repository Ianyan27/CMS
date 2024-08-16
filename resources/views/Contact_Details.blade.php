@extends('layouts.app')

@section('title', 'Contact Detail Page')

@section('content')
    <div class="container-fluid mb-2">
        <div class="row">
            <div class="col-md-12">
                <div class="table-title d-flex justify-content-between align-items-center mb-3">
                    <h2 class="ml-3 mb-2 font">Contact Detail Page</h2>
                    <a href="/editcontactdetail" class="btn hover-action mb-2" data-toggle="tooltip" title="Edit">
                        <i class="fa-solid fa-pen-to-square "></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="contact-detail-container mt-4 border-educ rounded p-3 bg-dashboard">
                    <h4 class="font">Contact Information</h4>
                    <p><strong>John Doe</strong></p>
                    <p>john.doe@example.com</p>
                    <p>123-456-7890</p>
                    <p>123 Main St, Anytown, USA 12345</p>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">
                        <div class="contact-detail-container mt-4 border-educ rounded p-3 bg-dashboard">
                            <h4 class="font">Email Activities</h4>
                            <ul class="list-unstyled fonts">
                                <li>
                                    <p><strong>2023-02-20</strong></p>
                                    <p>Sales Agent Emily Chen sent a promotional email to John Doe about our new product launch.</p>
                                </li>
                                <hr>
                                <li>
                                    <p><strong>2023-02-12</strong></p>
                                    <p>John Doe was sent a follow-up email with a case study of a similar business that benefited from our services.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="contact-detail-container mt-4 border-educ rounded p-3 bg-dashboard">
                            <h4 class="font">Phone Activities</h4>
                            <ul class="list-unstyled fonts">
                                <li>
                                    <p><strong>2023-02-10</strong></p>
                                    <p>Sales Agent Emily Chen followed up with a phone call to John Doe to discuss his needs and provide a personalized demo.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="contact-detail-container mt-4 border-educ rounded p-3 bg-dashboard">
                            <h4 class="font">Meeting Activities</h4>
                            <ul class="list-unstyled fonts">
                                <li>
                                    <p><strong>2023-02-18</strong></p>
                                    <p>Sales Agent Emily Chen scheduled a meeting with John Doe to discuss a customized solution for his business.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-12">
                        <div class="contact-detail-container mt-4 border-educ rounded p-3 bg-dashboard">
                            <h4 class="font">Promotions and Offers</h4>
                            <ul class="list-unstyled fonts">
                                <li>
                                    <p><strong>2023-02-22</strong></p>
                                    <p>John Doe was sent a special offer for 10% off our premium service.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection
