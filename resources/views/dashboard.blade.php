<x-app-layout>


    <div id="carouselExampleIndicators" class="carousel slide ml-5 mr-5 mt-0" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{URL('images/carousel.png')}}" class="d-block w-100 " style="height: 500px; object-fit: cover;" alt="...">
            </div>
            <div class="carousel-item">
                <img src="{{URL('images/carousel1.png')}}" class="d-block w-100 " style="height: 500px; object-fit: cover;" alt="...">
            </div>
            <div class="carousel-item">
                <img src="{{URL('images/carousel2.png')}}" class="d-block w-100 " style="height: 500px; object-fit: cover;" alt="...">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>


    <div class="mt-5 mb-5 mr-3 ml-3 d-flex flex-wrap justify-content-evenly">
        @if($allGames->count() > 0)

        @foreach($allGames as $game)
        <div class="card col-2 p-0 rounded-lg m-2" style="height: 400px;background-color:#22242c">
            <a href="{{route('viewItem', ['gameID' => $game->id])}}">
                <div class="card-body p-0 ">
                    <img src="{{$game->game_image}}" style="height: 200px; width:100%; object-fit: cover;" alt="">

                    <div class="p-3">
                        <h4 class="card-title text-white "><b>{{$game->game_title}}</b></h4>

                        <p class="card-text text-white">From MYR {{$game->game_price - ($game->game_discount  % $game->game_price )}}</p>
                        <div class="d-flex">
                            <h5 class="text-danger mb-1 text-decoration-line-through">MYR {{$game->game_price}}</h5><span class="text-muted mr-4"> - {{$game->game_discount}}%</span>
                        </div>

                    </div>

                    <span class="badge bg-primary mt-3 mb-1 w-50 ml-3">{{$game->game_store_type}}</span>

                </div>
            </a>
        </div>



        @endforeach

        @else
        <p class="mt-3">No results found.</p>

        @endif






    </div>


</x-app-layout>