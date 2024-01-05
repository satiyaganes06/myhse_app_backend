<x-app-layout>
    <style>
        label{
            color: white;
        }
    </style>

  <div class="container mt-5">
    <h2 class="mb-4 display-4 text-white text-center mb-5">Game Information Form</h2>
    
    <form action="{{route('addGameInfo')}}" method="post" style="margin-left: 20%; margin-right:20%">
    @csrf
      <div class="mb-3">
        <label for="gameTitle" class="form-label">Game Title</label>
        <input type="text" class="form-control rounded-3" name="game_title" required>
      </div>

      <div class="mb-3">
        <label for="gameStoreType" class="form-label">Game Store Type</label>
        <input type="text" class="form-control rounded-3" name="game_store_type" required>
      </div>

      <div class="mb-3">
        <label for="gamePrice" class="form-label">Game Price</label>
        <input type="number" class="form-control rounded-3" name="game_price" step="0.01" required>
      </div>

      <div class="mb-3">
        <label for="gameDiscount" class="form-label">Game Discount (%)</label>
        <input type="number" class="form-control rounded-3" name="game_discount" required>
      </div>

      <div class="mb-3">
        <label for="gameImage" class="form-label">Game Image URL</label>
        <input type="text" class="form-control rounded-3" name="game_image" required>
      </div>

      <div class="mb-3">
        <label for="gameVideoLink" class="form-label">Game Video Link</label>
        <input type="text" class="form-control rounded-3" name="game_video_link" required>
      </div>

      <div class="mb-3">
        <label for="gameDescription" class="form-label">Game Description</label>
        <textarea class="form-control rounded-3" name="game_description" rows="3" required></textarea>
      </div>

      <div class="mb-3">
        <label for="gameDeveloper" class="form-label">Game Developer</label>
        <input type="text" class="form-control rounded-3" name="game_developer" required>
      </div>

      <div class="mb-3">
        <label for="gamePublisher" class="form-label">Game Publisher</label>
        <input type="text" class="form-control rounded-3" name="game_publisher" required>
      </div>

      <div class="mb-3">
        <label for="gameReleaseDate" class="form-label">Game Release Date</label>
        <input type="date" class="form-control rounded-3" name="game_release_date" required>
      </div>

      <button type="submit" class="btn btn-primary mb-5 mt-5 btn-block">Submit</button>
    </form>
  </div>

 
</x-app-layout>
