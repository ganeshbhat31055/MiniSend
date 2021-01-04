<?php

namespace Tests\Feature;

use App\Models\Email;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailAPITest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test case for testing the API for sending the email
     * @dataProvider dataProvider
     * @return void
     */
    public function test_for_sending_email($from,$to,$subject,$html,$text,$attachement,$expected)
    {
        $this->withoutExceptionHandling();

        $data = [ 'from' => $from,'to'=>$to,'subject'=>$subject,'html'=>$html,'text'=>$text,'attachement'=>$attachement];

        $response = $this->post('/email/send',$data);

        $response->assertStatus(200);

        $response->assertJson($expected, false);


    }


    /**
     * @param $param
     * @param $search
     * @dataProvider dataSearchProvider
     */
    public function test_to_search_by_params($param,$search)
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/email/search?param='.$param.'&search='.$search);

        $response->assertOk();

        if ($param == 'null'){
            $emails = Email::paginate(10);
        }else{
            $emails = Email::where($param,'like','%'.$search.'%')->paginate(10);
        }

        $this->assertIsArray($response->json(),$emails);
    }

    public function test_to_get_dashboard_data()
    {

        $this->withoutExceptionHandling();

        $response = $this->get('/email/dashboard');

        $success = Email::where('status',0)->count();
        $failed = Email::where('status',1)->count();
        $posted = Email::where('status',2)->count();

        $response->assertJson([
            'success'=>$success,
            'failed'=>$failed,
            'posted'=>$posted
        ]);

    }

    public function test_to_show_email()
    {
        $this->withoutExceptionHandling();

        $data = [ 'from' => 'gan3@tr.com','to'=>'gan1@gmail.com','subject'=>'Happy New Year','html'=>null,'text'=>'Wish you a happy new year'];

        $response = $this->post('/email/send',$data);

        $response->assertStatus(200);

        $email = Email::first();

        $response = $this->get('/email/show/'.$email->id);

        $response->assertJson($data);

    }


    public function dataSearchProvider() : array
    {
        return [
            ['null','null'],
            ['to','ane'],
            ['from','ane'],
            ['subject','ane']
        ];
    }


    public function dataProvider(): array
    {
        return [
            ["gan","gan1@gmail.com","Message to Ganesh",null,"sdfasf","htng",
                ['status' => 'failure',
                'message' => 'Email in incorrect format']
            ],
            [null,"gan1@gmail.com","Message to Ganesh",null,"sdfasf","htng",
                ['status' => 'failure',
                    'message' => 'The from field is required.']
            ],
            ["gan@gmail.com",null,"Message to Ganesh",null,"sdfasf","htng",
                ['status' => 'failure',
                    'message' => 'The to field is required.']
            ],
            ["gan@gmail.com","gan@gmail.com","Message to Ganesh","html","sdfasf","htng",
                ['status' => 'success']
            ],
            ["gan@gmail.com","gan@gmail.com","subject","html",null,"html",
                ['status' => 'failure',
                'message' => 'The text field is required.']
            ],
        ];
    }
}
