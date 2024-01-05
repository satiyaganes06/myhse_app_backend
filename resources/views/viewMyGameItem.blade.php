<!-- BCS3453 [PROJECT]-SEMESTER 2324/1
Student ID: CB21132
Student Name: SHATTHIYA GANES A/L SIVAKUMARAN -->
<x-app-layout>


    <div class="text-white p-5 d-flex">
        <div class="w-75">
            <h1 class="display-6">{{$rentedGame->game_title}}</h1>

            <div class="d-flex ">

                <div class="rating">
                    @php
                    $rating = $rentedGame->game_rating;
                    $fullStars = floor($rating);
                    $halfStar = ($rating - $fullStars) > 0 ? true : false;
                    @endphp

                    {{-- Full stars --}}
                    @for ($i = 1; $i <= $fullStars; $i++) <i class="fas fa-star text-warning"></i>
                        @endfor

                        {{-- Half star --}}
                        @if ($halfStar)
                        <i class="fas fa-star-half-alt text-warning"></i>
                        @endif

                        {{-- Outline stars --}}
                        @for ($i = 1; $i <= (5 - $fullStars - ($halfStar ? 1 : 0)); $i++) <i class="far fa-star"></i>
                            @endfor
                </div>
                <span class="badge bg-secondary text-center" style="font-size: 16px;">{{$rentedGame->game_rating}}</span>
            </div>

            @php
            $videoLink = $rentedGame->game_video_link;
            $videoId = substr($videoLink, strrpos($videoLink, '=') + 1);

            // Construct the embed URL
            $embedUrl = "https://www.youtube.com/embed/$videoId";
            @endphp
            <iframe class="mt-3" width="860" height="515" src="{{ $embedUrl  }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            <p class="mt-3 text-muted" style="font-size:14px;text-align:justify; text-justify: inter-word;">{{$rentedGame->game_description}}</p>
        </div>

        <div class="ml-5 w-100 mr-5">
            <div style="height: 10%;"></div>

            <div>
                <div class="d-flex justify-content-between mt-2 mb-2">
                    <p class="text-muted">Status</p>
                    @if($rentedGame->status == "Rented")
                    <p class="text-success">{{ $rentedGame->status}}</p>

                    @elseif ($rentedGame->status == "Cancelled")
                    <p class="text-danger">{{ $rentedGame->status }}</p>

                    @endif
                </div>



                <hr class="bg-white">

                <div class="d-flex justify-content-between mt-2 mb-2">
                    <p class="text-muted">Rent from</p>
                    <p>{{$rentedGame->rentFrom}}</p>
                </div>

                <hr class="bg-white">

                <div class="d-flex justify-content-between mt-2 mb-2">
                    <p class="text-muted">Rent End</p>
                    <p>{{$rentedGame->rentTo}}</p>
                </div>

                <hr class="bg-white mb-3">

                <p class="text-center">Payment</p>

                <hr class="bg-white  mt-3">

                <div class="d-flex justify-content-between mt-2 mb-2">
                    <p class="text-muted">Card Name</p>
                    <p>{{$rentedGame->cardHolderName}}</p>
                </div>

                <hr class="bg-white">

                <div class="d-flex justify-content-between mt-2 mb-2">
                    <p class="text-muted">Card Number</p>

                    <p>{{$maskedCardNumber = substr($rentedGame->cardNumber, 0, 2) . str_repeat('*', strlen($rentedGame->cardNumber) - 4) . substr($rentedGame->cardNumber, -1)}}</p>
                </div>

                <hr class="bg-white">
            </div>


            @if($rentedGame->status == "Rented")
            <div class="d-grid gap-2 ">
                <a href="" data-bs-toggle="modal" data-bs-target="#exampleModal"><button class="btn btn-block mb-3 mt-5  text-white bg-danger "><b>Cancel</b></button></a>
            </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-danger" id="exampleModalLabel">Delete</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Do you want to cancel the rent? <i>There will be no refund.</i>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-dark" data-bs-dismiss="modal">Close</button>
                    <a href="{{route('updateMyGameItem', ['gameID' => $rentedGame->id])}}" class="btn-danger rounded-2"><button type="button" class="btn btn-danger  text-white">Cancel Rent</button></a>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>