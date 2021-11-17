<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DebugBar\DebugBar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

class MailController extends Controller
{

    private $JSdata;
    private $mail;
    private $type;
    private $title;
    private $body;
    private $toAddress;
    private $toName;
    private $attachment;
    private $modalOKClass = null;
    private $modalOKTitle = null;
    private $modalOKBody = null;
    private $modalOKButton1 = null;
    private $modalOKButton2 = null;
    private $modalKOClass = null;
    private $modalKOTitle = null;
    private $modalKOBody = null;
    private $modalKOButton1 = null;
    private $modalKOButton2 = null;
    private $html;


    public function __construct()
    {
        $this->mail = new PHPMailer(true);
    }

    public function sendFromRequest(Request $request)
    {
        // Gets sent variables variables
        $this->JSdata = $request->all();

        $this->type = $this->JSdata["type"];

        return $this->send();
    }

    /**
     * @param $type
     * @param null $title
     * @param null $body
     * @param null $button1
     * @param null $button2
     * @param null $toAddress
     * @param null $toName
     */
    public function sendThis($type, $title = null, $body = null, $toAddress = null, $toName = null, $attachment = null )
    {
        // Gets sent variables variables
        $this->type = $type;
        $this->title = $title;
        $this->body = $body;
        $this->toAddress = $toAddress;
        $this->toName = $toName;
        $this->attachment = $attachment;

        return $this->send();
    }

    public function send()
    {

        // set parameters configured for mail type
        switch ($this->type) {
            case "contact":
                $this->contact();
                break;

            case "document":
                $this->document();
                break;

            default:
                return false;
        }

        // Sends email

//      try {
        //Server settings
        $this->mail->SMTPDebug = 0;                                       // Enable verbose debug output
        $this->mail->isSMTP();                                            // Set mailer to use SMTP
        $this->mail->Host = config('mail.host');               // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $this->mail->Username = config('mail.username');           // SMTP username
        $this->mail->Password = config('mail.password');           // SMTP password
        $this->mail->SMTPSecure = config('mail.encryption');         // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = config('mail.port');               // TCP port to connect to
        $this->mail->CharSet = 'UTF-8';

        /*
        // Name is optional
        $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');


        // Attachments
        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        */

        // Content
        $this->mail->isHTML(true);                                  // Set email format to HTML

        // Fixes connection errors
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        if (!$this->mail->send()) {
            return response()->json(['error' => 'KO', 'customClass'=> $this->modalKOClass, 'title'=> $this->modalKOTitle, 'body'=> $this->modalKOBody, 'button'=> $this->modalKOButton1, 'e' => $this->mail->ErrorInfo]);
        } else {
            return response()->json(['success' => 'OK', 'customClass'=>  $this->modalOKClass, 'title'=> $this->modalOKTitle, 'body'=> $this->modalOKBody, 'button'=> $this->modalOKButton1]);
        }
        /*
        } catch (Exception $e) {
            return response()->json(['error'=> 'KO', 'customClass'=> 'contactFailed', 'title'=> __('contact.form.modal.title'), 'body'=> $e, 'button'=> __('contact.form.modal.button') ]);
        }*/

    }

    public function contact()
    {
        //Recipients
        $this->mail->setFrom(config('mail.from.contactForm.address'), config('mail.from.contactForm.name'));
        $this->mail->addAddress(config('mail.to.contactForm.address'), config('mail.to.contactForm.name'));
        // Sends an email with the data

        $this->mail->Subject = __('email.contact.form.subject');

        $this->mail->Body =
            'Nombre: ' . $this->JSdata['name'] . "<br>" .
            'Apellidos: ' . $this->JSdata['surname'] . "<br>" .
            'Telf: ' . $this->JSdata['phone'] . "<br>" .
            'E-Mail: ' . $this->JSdata['email'] . "<br>" .
            'Empresa: ' . $this->JSdata['company'] . "<br>" .
            'Puesto: ' . $this->JSdata['position'] . "<br>" .
            'CIF: ' . $this->JSdata['cif'] . "<br>" .
            'Privacidad: ' . $this->JSdata['rgpd'] . "<br>" .
            'Mensaje: ' . "<br>" . $this->JSdata['message'] . "<br>";

        $this->mail->AltBody =
            'Nombre: ' . $this->JSdata['name'] .
            'Apellidos: ' . $this->JSdata['surname'] .
            'Telf: ' . $this->JSdata['phone'] .
            'E-Mail: ' . $this->JSdata['email'] .
            'Empresa: ' . $this->JSdata['company'] .
            'Puesto: ' . $this->JSdata['position'] .
            'CIF: ' . $this->JSdata['cif'] .
            'Privacidad: ' . $this->JSdata['rgpd'] .
            'Mensaje: ' . $this->JSdata['message'];

        $this->modalOKClass = "contactSent";
        $this->modalOKTitle = __('contact.form.modal.title');
        $this->modalOKBody = __('contact.form.modal.body.sent');
        $this->modalOKButton1 = __('contact.form.modal.button');
        $this->modalKOClass = "contactFailed";
        $this->modalKOTitle = __('contact.form.modal.title');
        $this->modalKOBody = __('contact.form.modal.body.failed');
        $this->modalKOButton1 = __('contact.form.modal.button');
    }

    public function document()
    {
        //Recipients
        $this->mail->setFrom(config('mail.from.sendDocument.address'), config('mail.from.sendDocument.name'));
        $this->mail->addAddress($this->JSdata['email']);

        $this->mail->Subject = "Previsión Mallorquina - Información Seguro de " . $this->JSdata['product'] ;

        // Sends an email with the data

        $this->mail->Body =  $this->JSdata['body'] . "<br>";

        $this->mail->AltBody = $this->JSdata['body'];

        $this->mail->addAttachment($this->JSdata['attachment']);

        $this->modalOKClass = "documentSent";
        $this->modalOKTitle = __('mail.send.document.modal.title');
        $this->modalOKBody = __('mail.send.document.modal.body.sent');
        $this->modalOKButton1 = __('mail.send.document.modal.button');
        $this->modalKOClass = "documentFailed";
        $this->modalKOTitle = __('mail.send.document.modal.title');
        $this->modalKOBody = __('mail.send.document.modal.body.failed');
        $this->modalKOButton1 = __('mail.send.document.modal.button');
    }


    public function sendHTML(Request $request)
    {
        //app('debugbar')->info('En SendHTML');
        //app('debugbar')->info($request);
        // Gets sent variables variables
        $this->JSdata = $request->all();

        //$this->type = $this->JSdata["type"];
        $this->toAddress = $this->JSdata["email"];
        $this->body = $this->JSdata["body"];
        $this->html = $this->JSdata["html"];

        $this->generatePDF("coste-seguro");
        $this->document();

        //Server settings
        $this->mail->SMTPDebug = 0;                                       // Enable verbose debug output
        $this->mail->isSMTP();                                            // Set mailer to use SMTP
        $this->mail->Host = config('mail.host');               // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $this->mail->Username = config('mail.username');           // SMTP username
        $this->mail->Password = config('mail.password');           // SMTP password
        $this->mail->SMTPSecure = config('mail.encryption');         // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = config('mail.port');               // TCP port to connect to
        $this->mail->CharSet = 'UTF-8';

        // Content
        $this->mail->isHTML(true);                                  // Set email format to HTML

        // Fixes connection errors
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        if (!$this->mail->send()) {
            unlink('coste-seguro.pdf');
            return response()->json(['error' => 'KO', 'customClass'=> $this->modalKOClass, 'title'=> $this->modalKOTitle, 'body'=> $this->modalKOBody, 'button'=> $this->modalKOButton1, 'e' => $this->mail->ErrorInfo]);
        } else {
            unlink('coste-seguro.pdf');
            return response()->json(['success' => 'OK', 'customClass'=>  $this->modalOKClass, 'title'=> $this->modalOKTitle, 'body'=> $this->modalOKBody, 'button'=> $this->modalOKButton1]);
        }

    }



    public function generatePDF($title)
    {

        //app('debugbar')->info('En generatePDF');
        //app('debugbar')->info($this->html);

        $html_content = file_get_contents('../resources/views/app/mail/layouts/prices.table.php');
		if ($html_content){
			$content = str_replace(
						array("{TITLE}", "{DATA_TABLE}"),
						array($title, $this->html),
						$html_content);
        }

        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        //Esto es necesario para que funcionen las css en el pdf
        $options->setDpi(150);
        $dompdf = new Dompdf($options);
        $dompdf->setBasePath(url()->current());
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        file_put_contents('coste-seguro.pdf', $dompdf->output());
        $this->JSdata['attachment'] = 'coste-seguro.pdf';
    }

    public function sendBudget(Request $request)
    {
        app('debugbar')->info('En SendHTML');
        app('debugbar')->info($request);
        // Gets sent variables variables
        $this->JSdata = $request->all();

        //$this->type = $this->JSdata["type"];
        $this->toAddress = $this->JSdata["email"];
        $this->body = $this->JSdata["body"];
        $this->html = $this->JSdata["html"];
        $this->JSdata['attachment'] = $this->JSdata['budgetURL'];

        //$this->generatePDF("coste-seguro");
        $this->document();

        //Server settings
        $this->mail->SMTPDebug = 0;                                       // Enable verbose debug output
        $this->mail->isSMTP();                                            // Set mailer to use SMTP
        $this->mail->Host = config('mail.host');               // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $this->mail->Username = config('mail.username');           // SMTP username
        $this->mail->Password = config('mail.password');           // SMTP password
        $this->mail->SMTPSecure = config('mail.encryption');         // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = config('mail.port');               // TCP port to connect to
        $this->mail->CharSet = 'UTF-8';

        // Content
        $this->mail->isHTML(true);                                  // Set email format to HTML

        // Fixes connection errors
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        if (!$this->mail->send()) {
            //unlink($this->JSdata['budgetURL']);
            return response()->json(['error' => 'KO', 'customClass'=> $this->modalKOClass, 'title'=> $this->modalKOTitle, 'body'=> $this->modalKOBody, 'button'=> $this->modalKOButton1, 'e' => $this->mail->ErrorInfo]);
        } else {
            //unlink($this->JSdata['budgetURL']);
            return response()->json(['success' => 'OK', 'customClass'=>  $this->modalOKClass, 'title'=> $this->modalOKTitle, 'body'=> $this->modalOKBody, 'button'=> $this->modalOKButton1]);
        }

    }

}
