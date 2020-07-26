<body style="font-family:arial;font-size:9pt">
    Olá <?=$user['full_name']?>,<br/>
    Você esqueceu sua senha, utilize o código abaixo para criar uma nova senha.<br/>
    <br/>
    <br/>
    <span style="background-color:#b1cd49;color:#fff;font-weight:weight;font-size:12pt;text-decoration:none;padding:15px;margin:20px 0;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;">
    <?=$code?>
    </span>
    <br/>
    <br/>
    <span style="color:#af1e73"><strong>[Atenção]:</strong> Caso você não tenha solicitado uma nova senha, por favor entre em contato com nossa equipe pelo e-mail <a href="mailto:<?=env('MAIL_SUPPORT')?>" style="color:#f00"><?=env('MAIL_SUPPORT')?></a></span>
    <br/>
    <div>
        <br/>
        Atenciosamente.
        <br/>
        <br/>
        <img src="<?=$message->embed(asset('images/logo_mail.png'))?>" width="150"/><br/>
        <strong>Equipe do Meu Jogo</strong><br/>
        <span style="color:#333;font-size:9pt;">
            <?=env('MAIL_SUPPORT')?><br/>
            sitedomeujogo.com.br<br/>
        </span>
    </div>
</body>

