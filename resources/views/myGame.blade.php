<!-- BCS3453 [PROJECT]-SEMESTER 2324/1
Student ID: CB21132
Student Name: SHATTHIYA GANES A/L SIVAKUMARAN -->
<x-app-layout>
    <style>
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>

    <div class="d-flex flex-column justify-content-center align-items-center mt-5">
        <p class="display-6 mb-5 text-white">My Game</p>
        <table class="table align-middle w-75 mb-5 rounded-2 text-white border" style="background-color: #55565E">
            <thead class="">
                <tr>
                    <th>Game ID</th>
                    <th>Game Name</th>
                    <th>Total Price (RM)</th>
                    <th>Status</th>
                    <th>Operation</th>
                </tr>
            </thead>
            <tbody>
                @if($rentedGames->count() > 0)

                @foreach($rentedGames as $rentedGame)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <p class="fw-normal mb-1">{{$rentedGame->gameID}}</p>
                        </div>
                    </td>
                    <td>
                        <p class="fw-normal mb-1 w-75">{{$rentedGame->game_title}}</p>
                    </td>
                    <td>

                        <p>{{$rentedGame->totalPrice}}</p>

                    </td>
                    <td>

                        @if($rentedGame->status == "Rented")
                        <p class="text-success">{{ $rentedGame->status}}</p>

                        @elseif ($rentedGame->status == "Cancelled")
                        <p class="text-danger">{{ $rentedGame->status }}</p>

                        @endif

                    </td>
                    <td class="pl-4">
                        <a href="{{route('myGameItem', ['gameID' => $rentedGame->id])}}"><i class="fas fa-eye text-white mr-2"></i></a>

                        @if ($rentedGame->status == "Cancelled")
                        <a href="{{route('deleteMyGameItem', ['rentID' => $rentedGame->rentID])}}" >
                            <i class="far fa-circle-xmark text-danger"></i>
                        </a>

                        @endif

                    </td>
                    
                </tr>



                @endforeach

                @else
                <tr>
                    <td></td>
                    <td></td>

                    <td>
                        <p class="mt-3">No results found.</p>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endif


            </tbody>
        </table>
    </div>



</x-app-layout>