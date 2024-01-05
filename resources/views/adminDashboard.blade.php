<x-app-layout>

    <div class="d-flex justify-content-between mt-5 mr-3 ml-3">
        <h1 class="text-white display-4 ml-5">All Games</h1>
    </div>   
    
    <div class='d-flex justify-content-end mr-5'>
        <a href="{{route('addGame')}}" class="text-white  btn rounded-5" style="background-color: #0000ee; margin-right:10px"><i class="fas fa-plus mr-3"></i> Add Game</a>
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