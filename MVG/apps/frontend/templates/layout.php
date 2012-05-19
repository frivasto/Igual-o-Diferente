<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta charset="utf-8" />
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body>
    <h1>Music Video Game</h1>
    <?php echo $sf_content ?>

    <div id="fb-root"></div>

    <script>
//         window.fbAsyncInit = function() {
//          FB.init({
//            appId      : '424542557563209',
//            status     : true,
//           cookie     : true,
//            xfbml      : true,
//            oauth      : true
//          });
//        };
//        (function(d){
//           var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
//           js = d.createElement('script'); js.id = id; js.async = true;
//           js.src = "//connect.facebook.net/en_US/all.js";
//           d.getElementsByTagName('head')[0].appendChild(js);
//         }(document));


      window.fbAsyncInit = function() {
        FB.init({
          appId: '424542557563209',
          cookie: true,
          xfbml: true,
          oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
          window.location.reload();
        });
        FB.Event.subscribe('auth.logout', function(response) {
          window.location.reload();
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>

  </body>
</html>
