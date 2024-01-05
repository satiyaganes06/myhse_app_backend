<!-- BCS3453 [PROJECT]-SEMESTER 2324/1
Student ID: CB21132
Student Name: SHATTHIYA GANES A/L SIVAKUMARAN -->
<x-app-layout>
    <style>
        .container {
            position: relative;
            background-image: url("images/bg.png");
            background-size: cover;
            padding: 25px;
            border-radius: 28px;
            max-width: 380px;
            width: 100%;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        header,
        .logo {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo img {
            width: 48px;
            margin-right: 10px;
        }

        h5 {
            font-size: 16px;
            font-weight: 400;
            color: #fff;
        }

        header .chip {
            width: 60px;
        }

        h6 {
            color: #fff;
            font-size: 10px;
            font-weight: 400;
        }

        h5.number {
            margin-top: 4px;
            font-size: 18px;
            letter-spacing: 1px;
        }

        h5.name {
            margin-top: 20px;
        }

        .container .card-details {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
    </style>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <form action="{{route('rentingGame', ['gameID' => $gameID])}}" method="post" accept-charset="utf-8" enctype="multipart/form-data" style="margin-left: 20%; margin-right:20%">
        @csrf

        <p class="display-5 text-white mt-5 mb-4">Rent Game</p>


        <!-- Name -->
        <div class="form-group">
            <label for="name" class="text-white">Name</label>
            <input type="text" class="form-control bg-dark text-white border-white rounded-3" disabled id="name" name="name" default value="{{ auth()->user()->name }}">
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="text-white">Email</label>
            <input type="email" class="form-control bg-dark text-white border-white rounded-3" disabled id="email" name="email" value="{{ auth()->user()->email }}">
        </div>

        <div class="form-group">
            <label for="gameID" class="text-white">Game ID</label>
            <input type="number" class="form-control bg-dark text-white border-white rounded-3" disabled id="gameID" name="gameID" value="{{$gameID}}">
        </div>

        <div class="d-flex justify-content-between">
            <div class="form-group w-50 mr-5">
                <label for="From" class="text-white">Rent From</label>
                <input type="date" class="form-control bg-dark text-white border-white rounded-3 " id="fromDate" name="fromDate" required>
            </div>

            <div class="form-group w-50">
                <label for="to" class="text-white">Rent To</label>
                <input type="date" class="form-control bg-dark text-white border-white rounded-3" id="toDate" name="toDate" required>
            </div>
        </div>

        <div class="d-flex flex-column justify-content-end align-items-end w-100 mt-3">
            <p for="totalPrice" class="text-white">Total Pric</p>
            <p class="text-white display-6">RM ???</p>
        </div>

        <hr class="bg-white mt-3 mb-3">


        <div class="form-group">
            <label for="cardHolderName" class="text-white">Name</label>
            <input type="text" class="form-control bg-dark text-white border-white rounded-3" id="cardHolderName" placeholder="SHATTHIYA GANES A/L SIVA" name="cardHolderName" required>
        </div>


        <div class="form-group">
            <label for="cardNumber" class="text-white">Card Number</label>
            <input type="text" class="form-control bg-dark text-white border-white rounded-3" id="cardNumber" placeholder="40" name="cardNumber" required>
        </div>

        <!-- Cover Letter Document -->
        <div class="d-flex">
            <div class="form-group column">
                <label for="expiration" class="text-white">Expiration</label><br>
                <input type="number" class="text-white bg-dark  border-white rounded-3" name="expiration" id="expiration" placeholder="12/21" required>
            </div>
            <div class="form-group ml-4 ">
                <label for="securityCode" class="text-white">Security Code</label><br>
                <input type="number" class="bg-dark text-white border-white rounded-3" name="securityCode" id="securityCode" placeholder="132" required>
            </div>
        </div>


        <div class="d-flex justify-content-center mt-5 mb-5">
            <button type="submit" class="btn text-white btn-block mr-5 ml-5 rounded-3" style="background-color: #0000ee;"><b>Rent</b></button>
        </div>


    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var fromDateInput = document.getElementById('fromDate');
            var toDateInput = document.getElementById('toDate');
            var totalPriceDisplay = document.querySelector('.display-6');

            function calculateTotalPrice() {
                var fromDateValue = new Date(fromDateInput.value);
                var toDateValue = new Date(toDateInput.value);

                var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
                var daysDifference = Math.round((toDateValue - fromDateValue) / oneDay);

                var pricePerDay = 5; // Change this to your actual price per day

                var totalPrice = daysDifference * pricePerDay;
                if (totalPrice < 0) {
                    totalPrice.textContent = 0;
                } else {
                    totalPriceDisplay.textContent = 'RM ' + totalPrice.toFixed(2);
                }

            }

            fromDateInput.addEventListener('input', calculateTotalPrice);
            toDateInput.addEventListener('input', calculateTotalPrice);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var expirationInput = document.getElementById('expiration');

            expirationInput.addEventListener('input', function() {
                var inputValue = expirationInput.value;

                // Check if the input value matches the pattern MM/YY
                var match = inputValue.match(/^(\d{1,2})\/(\d{2})$/);

                if (match) {
                    var currentMonth = parseInt(match[1]);
                    var currentYear = parseInt(match[2]);

                    // Assume a default validity period of 2 years
                    var updatedYear = currentYear + 2;

                    // Ensure the year is within a two-digit range
                    updatedYear = updatedYear % 100;

                    // Update the input value with the new expiration date
                    expirationInput.value = currentMonth.toString().padStart(2, '0') + '/' + updatedYear.toString().padStart(2, '0');
                }
            });
        });
    </script>
</x-app-layout>