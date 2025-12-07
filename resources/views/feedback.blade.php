@extends('layouts.public')

@section('title', 'Feedback')

@section('content')
<section class="fedback">
    <img src="{{ asset('images/admin-log-bg.jpg') }}" alt="" class="fed">
    <div class="fed-box">
        <div class="lhs">
            <h3>Send your feedbacks</h3>
            <form name="feedback_form" id="feedback_form" method="post" action="#" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <input type="text" placeholder="Enter name*" name="feedback_name" id="feedback_name" class="form-control" required="required">
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Enter email*" required="required" name="feedback_email" pattern="^[\w]{1,}[\w.+-]{0,}@[\w-]{2,}([.][a-zA-Z]{2,}|[.][\w-]{2,}[.][a-zA-Z]{2,})$" title="Invalid email address">
                </div>
                <div class="form-group">
                    <input type="text" onkeypress="return isNumber(event)" class="form-control" id="feedback_mobile" name="feedback_mobile" placeholder="Enter mobile number *" pattern="[7-9]{1}[0-9]{9}" title="Phone number starting with 7-9 and remaining 9 digit with 0-9" required="">
                </div>
                <div class="form-group">
                    <textarea name="feedback_message" id="feedback_message" required="required" placeholder="Write your feedback here*"></textarea>
                </div>
                <button type="submit" id="feedback_submit" name="feedback_submit" class="btn btn-primary">Submit Feedback</button>
            </form>
        </div>
        <div class="rhs">
            <h2>Your feedback</h2>
            <p>Your feedback is most important for us. There are many variations of passages of Lorem Ipsum available,but the majority have suffered alteration in some form, by injected humour</p>
            <ul>
                <li><a href="#"><img src="{{ asset('images/icon/facebook.png') }}" alt="" loading="lazy"></a></li>
                <li><a href="#"><img src="{{ asset('images/icon/twitter.png') }}" alt="" loading="lazy"></a></li>
                <li><a href="#"><img src="{{ asset('images/icon/linkedin.png') }}" alt="" loading="lazy"></a></li>
                <li><a href="#"><img src="{{ asset('images/icon/whatsapp.png') }}" alt="" loading="lazy"></a></li>
            </ul>
            <h4>Why send feedback?</h4>
            <p>Useful for feature update</p>
            <p>Helping for customer feedback</p>
        </div>
    </div>
</section>
@endsection
