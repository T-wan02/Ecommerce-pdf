@extends('master')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/style/list.css') }}">
@endsection

@section('content')
    @include('nav')

    <section class="hero-section">
        <div class="navigate" style="margin-top: 10rem;">Guide > Test</div>

        <div class="navigate-line"></div>

        <div class="list-container">
            @foreach ($products as $p)
                <a href="{{ url('/p/' . $p->slug) }}" class="item">
                    <img src="{{ asset('assets/vendor/images/pdf.jpg') }}" class="item-img" alt="pdf-file">
                    <div class="info-container">
                        <h3 class="item-name">{{ $p->name }}</h3>
                        <span class="item-price">$ {{ $p->price }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- <a href="{{ asset('pdfs/Wan Wan Resume (1).pdf') }}" download>pdf</a> --}}

    <section class="sign-up-form">
        <h4>Sign up with your email to receive updates</h4>
        <div class="sign-up-form-container">
            <input type="text" placeholder="Email address">
            <button class="fill_btn">Sign Up</button>
        </div>
    </section>
@endsection
