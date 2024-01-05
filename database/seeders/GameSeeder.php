<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use Faker\Factory as Faker;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $faker = Faker::create();

        // for ($i = 0; $i < 10; $i++) {
        //     Game::create([
        //         'game_title' => $faker->sentence,
        //         'game_rating' => $faker->randomFloat(2, 1, 10),
        //         'game_store_type' => $faker->word,
        //         'game_price' => $faker->randomFloat(2, 10, 100),
        //         'game_discount' => $faker->numberBetween(0, 50),
        //         'game_image' => $faker->imageUrl,
        //         'game_video_link' => $faker->url,
        //         'game_description' => $faker->paragraph,
        //         'game_developer' => $faker->company,
        //         'game_publisher' => $faker->company,
        //         'game_release_date' => $faker->date,
        //     ]);
        // }
        $games = [


            [
                'game_title' => 'Super Mario',
                'game_rating' => 3.5,
                'game_store_type' => 'In-Game Content',
                'game_price' => 100.00,
                'game_discount' => 30,
                'game_image' => 'https://media.wired.com/photos/64371550e0070e81ad725800/master/pass/Super-Mario-Bros-Movie-Success-Is-Impossible-To-Replicate-Culture-2530_T2_00041.jpg',
                'game_video_link' => 'https://www.youtube.com/watch?v=60O982jyJ6U',
                'game_description' => 'Mario[f] is a character created by the Japanese video game designer Shigeru Miyamoto. He is the title character of the Mario franchise and the mascot of the Japanese video game company Nintendo. Mario is an Italian plumber who resides in the Mushroom Kingdom with his younger twin brother, Luigi. Their adventures generally center on rescuing Princess Peach from the villain Bowser while using power-ups that give them different abilities.',
                'game_developer' => 'Team Gatt',
                'game_publisher' => 'Team Gatt',
                'game_release_date' => '2021-10-10',
            ],

            [
                'game_title' => 'Red Dead Redemption 2',
                'game_rating' => 4.5,
                'game_store_type' => 'In-Game Content',
                'game_price' => 100.00,
                'game_discount' => 50,
                'game_image' => 'https://image.api.playstation.com/gs2-sec/appkgo/prod/CUSA08519_00/12/i_3da1cf7c41dc7652f9b639e1680d96436773658668c7dc3930c441291095713b/i/icon0.png',
                'game_video_link' => 'https://www.youtube.com/watch?v=KlylKoFgl80',
                'game_description' => 'Red Dead Redemption 2 is a 2018 action-adventure game developed and published by Rockstar Games. The game is the third entry in the Red Dead series and is a prequel to the 2010 game Red Dead Redemption. The story is set in 1899 in a fictionalized representation of the Western, Midwestern, and Southern United States and follows outlaw Arthur Morgan, a member of the Van der Linde gang. Arthur must deal with the decline of the Wild West whilst attempting to survive against government forces, rival gangs, and other adversaries. The story also follows fellow gang member John Marston, the protagonist of Red Dead Redemption.',
                'game_developer' => 'Rockstar Games',
                'game_publisher' => 'Rockstar Games',
                'game_release_date' => '2021-10-10',
            ],

            [
                'game_title' => 'The Legend of Zelda: Breath of the Wild',
                'game_rating' => 3,
                'game_store_type' => 'Full Game',
                'game_price' => 120.00,
                'game_discount' => 10,
                'game_image' => 'https://assets-prd.ignimgs.com/2022/06/14/zelda-breath-of-the-wild-1655249167687.jpg',
                'game_video_link' => 'https://www.youtube.com/watch?v=1rPxiXXxftE',
                'game_description' => 'The Legend of Zelda: Breath of the Wild is an action-adventure game developed and published by Nintendo. It is the nineteenth installment in The Legend of Zelda series. The game story follows Link, who awakens from a hundred-year slumber to defeat Calamity Ganon and save the kingdom of Hyrule.',
                'game_developer' => 'Nintendo',
                'game_publisher' => 'Nintendo',
                'game_release_date' => '2017-03-03',
            ],

            [
                'game_title' => 'Cyberpunk 2077',
                'game_rating' => 1.5,
                'game_store_type' => 'Full Game',
                'game_price' => 80.00,
                'game_discount' => 20,
                'game_image' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Cyberpunk_2077_box_art.jpg',
                'game_video_link' => 'https://www.youtube.com/watch?v=8X2kIfS6fb8',
                'game_description' => 'Cyberpunk 2077 is an action role-playing game developed and published by CD Projekt. The game is set in an open-world metropolis called Night City, in the dystopian future of the year 2077. Players assume the role of V, a mercenary in pursuit of immortality, as they navigate the dangerous world of corporate megacorps and cyber-enhanced street gangs.',
                'game_developer' => 'CD Projekt',
                'game_publisher' => 'CD Projekt',
                'game_release_date' => '2020-12-10',
            ],

            [
                'game_title' => 'Grand Theft Auto V',
                'game_rating' => 5,
                'game_store_type' => 'Full Game',
                'game_price' => 80.00,
                'game_discount' => 5,
                'game_image' => 'https://upload.wikimedia.org/wikipedia/ms/a/a5/Grand_Theft_Auto_V.png',
                'game_video_link' => 'https://www.youtube.com/watch?v=5z-VhDmQGdM',
                'game_description' => 'Grand Theft Auto V is a 2013 action-adventure game developed by Rockstar North and published by Rockstar Games. It is the first main entry in the Grand Theft Auto series since 2008s Grand Theft Auto IV. Set within the fictional state of San Andreas, based on Southern California, the single-player story follows three protagonists—retired bank robber Michael De Santa, street gangster Franklin Clinton, and drug dealer and arms smuggler Trevor Philips—and their efforts to commit heists while under pressure from a corrupt government agency and powerful criminals.',
                'game_developer' => 'Rockstar Games',
                'game_publisher' => 'Rockstar Games',
                'game_release_date' => '2013-09-17',
            ],

            [
                'game_title' => 'Overwatch',
                'game_rating' => 2.5,
                'game_store_type' => 'Full Game',
                'game_price' => 60.00,
                'game_discount' => 25,
                'game_image' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/51/Overwatch_cover_art.jpg/220px-Overwatch_cover_art.jpg',
                'game_video_link' => 'https://www.youtube.com/watch?v=lkTHRdZzl1o',
                'game_description' => 'Overwatch is a team-based multiplayer first-person shooter developed and published by Blizzard Entertainment. The game features a diverse cast of characters, each with unique abilities, and players engage in team-based battles across various maps and objectives.',
                'game_developer' => 'Blizzard Entertainment',
                'game_publisher' => 'Blizzard Entertainment',
                'game_release_date' => '2016-05-24',
            ],

            [
                'game_title' => 'Minecraft',
                'game_rating' => 4.5,
                'game_store_type' => 'Full Game',
                'game_price' => 30.00,
                'game_discount' => 15,
                'game_image' => 'https://image.api.playstation.com/vulcan/img/cfn/11307HDYQLInHUkUC8WJwfsExISfTWdatsFPRxE3UqeG3b8AV193V941R62r7jE4pJUkyiz5ijf6DS3KYq2LZaiCWW81j3b6.png',
                'game_video_link' => 'https://www.youtube.com/watch?v=yoDwQsPNUE8',
                'game_description' => 'Minecraft is a sandbox game developed and published by Mojang Studios. Players can explore a blocky, procedurally-generated 3D world with infinite terrain, discovering and extracting raw materials, crafting tools and items, and building structures or earthworks.',
                'game_developer' => 'Mojang Studios',
                'game_publisher' => 'Mojang Studios',
                'game_release_date' => '2011-11-18',
            ],

            [
                'game_title' => 'FIFA 22',
                'game_rating' => 5,
                'game_store_type' => 'Full Game',
                'game_price' => 70.00,
                'game_discount' => 50,
                'game_image' => 'https://image.api.playstation.com/vulcan/img/rnd/202109/2115/5jeuJiXUpb1bZc0Lp1U8N5Ka.png',
                'game_video_link' => 'https://www.youtube.com/watch?v=o1igaMv46SY',
                'game_description' => 'FIFA 22 is a football simulation video game developed by EA Vancouver and published by Electronic Arts. It is the latest installment in the FIFA series, featuring updated gameplay mechanics, improved graphics, and new modes for football enthusiasts.',
                'game_developer' => 'EA Vancouver',
                'game_publisher' => 'Electronic Arts',
                'game_release_date' => '2021-10-01',
            ],

            [
                'game_title' => 'Doom Eternal',
                'game_rating' => 3,
                'game_store_type' => 'Full Game',
                'game_price' => 50.00,
                'game_discount' => 70,
                'game_image' => 'https://image.api.playstation.com/vulcan/ap/rnd/202010/0114/b4Q1XWYaTdJLUvRuALuqr0wP.png',
                'game_video_link' => 'https://www.youtube.com/watch?v=FkklG9MA0vM',
                'game_description' => 'Doom Eternal is a first-person shooter game developed by id Software and published by Bethesda Softworks. It is the fifth main game in the Doom series and a direct sequel to Doom (2016). Players take on the role of the Doom Slayer, battling against hordes of demons from Hell.',
                'game_developer' => 'id Software',
                'game_publisher' => 'Bethesda Softworks',
                'game_release_date' => '2020-03-20',
            ],

            [
                'game_title' => 'Star Wars Jedi: Fallen Order',
                'game_rating' => 2,
                'game_store_type' => 'Full Game',
                'game_price' => 80.00,
                'game_discount' => 40,
                'game_image' => 'https://image.api.playstation.com/vulcan/img/rnd/202105/1714/WHeOu95nW2SZQy6H5IKgE2Bg.png',
                'game_video_link' => 'https://www.youtube.com/watch?v=0GLbwkfhYZk',
                'game_description' => 'Star Wars Jedi: Fallen Order is an action-adventure game developed by Respawn Entertainment and published by Electronic Arts. Set in the Star Wars universe, players control Cal Kestis, a young Jedi Padawan who must navigate the galaxy, evade the Empire, and rebuild the Jedi Order.',
                'game_developer' => 'Respawn Entertainment',
                'game_publisher' => 'Electronic Arts',
                'game_release_date' => '2019-11-15',
            ]
        ];


        foreach ($games as $key => $game) {
            Game::create($game);
        }
    }
}
