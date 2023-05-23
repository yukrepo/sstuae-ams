@extends('layouts.main')
@section('content')
    <div class="">
        <div class="container-fluid">
            <div class="row synop mb-3">
                <div class="col-md-12 col-sm-12 col-12 m-b-20 text-center">
                    <div class="center">
                        <div class="circle1"></div>
                        <div class="circle2"></div>
                        <div class="circle3"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection




<style>
    @keyframes bounce-6 {
    0%   { transform: scale(1,1)      translateY(0); }
    10%  { transform: scale(1.1,.9)   translateY(0); }
    30%  { transform: scale(.9,1.1)   translateY(-30px); }
    50%  { transform: scale(1.05,.95) translateY(0); }
    57%  { transform: scale(1,1)      translateY(-7px); }
    64%  { transform: scale(1,1)      translateY(0); }
    100% { transform: scale(1,1)      translateY(0); }
}
@keyframes animateCircle1
{
    0%{ transform: rotate(0deg);}
    100%{ transform: rotate(360deg);}
}
@keyframes animateCircle2
{
    0%{ transform: rotate(360deg);}
    100%{ transform: rotate(0deg);}
}

.center{position: relative; left:50%; transform: translate(-40%, 50%); width: 300px; height:300px; box-sizing: border-box;}
.circle1{width: 100%;
    height: 100%;
    border-radius: 50%;
    border-style: dotted;
    border-width: 2px;
    border-color: #06A23F;
    box-sizing: border-box;
    -webkit-animation: animateCircle1 15s linear infinite;
    animation: animateCircle1 15s linear infinite;
    background: #fff;
    box-shadow: 0px 0px 55px 1px rgba(6, 162, 63, 0.51);}
.circle2{width: calc(100% - 32px);
    height: calc(100% - 32px);
    position: absolute;
    top: 16px;
    left: 16px;
    border-radius: 50%;
    border-width: 2px;
    border-style: dotted;
    border-color: #151E5F;
    -webkit-animation: animateCircle2 13s linear infinite;
    animation: animateCircle2 13s linear infinite;
    background: #fff;}
.circle3{position: absolute; top: 100px; left: 60px; width: calc(100% - 40px); height: calc(100% - 40px); background-size: cover; background: url('images/big-logo.png') no-repeat; }
</style>
