<!-- BCS3453 [PROJECT]-SEMESTER 2324/1
Student ID: CB21132
Student Name: SHATTHIYA GANES A/L SIVAKUMARAN -->
<x-app-layout>

    <div class="text-white p-5 d-flex">
        <div class="w-75">
            <h1 class="display-6">{{$gameItem->game_title}}</h1>

            <div class="rating">
                @php
                $rating = $gameItem->game_rating;
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

            @php
            $videoLink = $gameItem->game_video_link;
            $videoId = substr($videoLink, strrpos($videoLink, '=') + 1);

            // Construct the embed URL
            $embedUrl = "https://www.youtube.com/embed/$videoId";
            @endphp
            <iframe class="mt-3" width="860" height="515" src="{{ $embedUrl  }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>


            <p class="mt-3 text-muted" style="font-size:14px;text-align:justify; text-justify: inter-word;">{{$gameItem->game_description}}</p>
        </div>

        <div class="ml-5 w-100 mr-5">
            <div style="height: 15%;"></div>
            <span class="badge bg-secondary text-center p-2" style="font-size: 12px;">{{$gameItem->game_store_type}}</span>

            <div class="d-flex mt-2">
                <span class="badge bg-danger text-center p-2 mr-2" style="font-size: 12px;">- {{$gameItem->game_discount}}</span>
                <p class="text-danger text-decoration-line-through mr-2">MYR {{$gameItem->game_price}}</p>
                <p class=" text-white mr-2">MYR {{$gameItem->game_price - ($gameItem->game_discount  % $gameItem->game_price )}}</p>

            </div>

            <p class="text-muted mt-2 mb-5" style="font-size: 12px;">Sale ends 1/11/2024 at 12:00 AM</p>


            @if(Auth::user()->role == "user")
            <a href="{{ route('rentGame', ['gameID' => $gameItem->id]) }}"><button class="btn btn-block pt-3 pb-3 mb-3 text-white" style="background-color: #0000ee;"><b>Rent now</b></button></a>
            @endif

            @if(Auth::user()->role == "admin")
            <a href="{{ route('deleteGame', ['gameID' => $gameItem->id]) }}"><button class="btn btn-block pt-3 pb-3 mb-3 text-white" style="background-color: red;"><b>Delete</b></button></a>
            @endif
            <div>
                <div class="d-flex justify-content-between mt-2 mb-2">
                    <p class="text-muted">Rewards</p>
                    <p>Earn 10% Back <i class="fas fa-star text-danger"></i></p>
                </div>

                <hr class="bg-white">

                <div class="d-flex justify-content-between mt-2 mb-2">
                    <p class="text-muted">Refund Type</p>
                    <p>Self-Refundable</p>
                </div>

                <hr class="bg-white">

                <div class="d-flex justify-content-between mt-2 mb-2">
                    <p class="text-muted">Developer</p>
                    <p>Rockstar Games</p>
                </div>

                <hr class="bg-white">

                <div class="d-flex justify-content-between mt-2 mb-2">
                    <p class="text-muted">Publisher</p>
                    <p>Rockstar Games</p>
                </div>

                <hr class="bg-white">

                <div class="d-flex justify-content-between mt-2 mb-2">
                    <p class="text-muted">Release Date</p>
                    <p>11/05/19</p>
                </div>

                <hr class="bg-white">
            </div>

        </div>
    </div>


</x-app-layout>