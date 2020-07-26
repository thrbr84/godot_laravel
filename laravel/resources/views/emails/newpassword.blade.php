<body style="font-family:arial;font-size:9pt">
    Olá <?=$user['full_name']?>,<br/>
    Sua nova senha foi criada com sucesso!.<br/>
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
