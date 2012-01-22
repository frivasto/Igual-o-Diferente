 <div id="ytapiplayer">
    You need Flash player 8+ and JavaScript enabled to view this video.
  </div>

  <script type="text/javascript">

    var params = { allowScriptAccess: "always" };
    var atts = { id: "myytplayer" };
    swfobject.embedSWF("http://www.youtube.com/apiplayer?enablejsapi=1",
                       "ytapiplayer", "425", "356", "8", null, null, params, atts);
  




  function onYouTubePlayerReady(playerId) {

      ytplayer = document.getElementById("myytplayer");
      ytplayer.loadVideoById("iQqK2onobes");
      ytplayer.seekTo(150,true);
      ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
      ytplayer.playVideo();

       

    }

    function play() {

      ytplayer = document.getElementById("myytplayer");
      if (ytplayer) {
          ytplayer.loadVideoById("29SuuEKztPc");
            ytplayer.playVideo();
          }
    }


    function onytplayerStateChange(newState) {
      // alert("Player's new state: " + newState);
    }

  </script>

<a href="javascript:void(0);" onclick="play();">Next</a>

    <div id="content">
    </div>
<div>
    <form action="" method="get" onsubmit="comet.doRequest($('word').value);$('word').value='';return false;">
            <input type="text" name="word" id="word" value="" />
            <input type="submit" name="submit" value="Send" />
    </form>

</div>

<script type="text/javascript">
var Comet = Class.create();
Comet.prototype = {

  timestamp: 0,
  url: './comet',
  noerror: true,

  initialize: function() { },

  connect: function()
  {
    this.ajax = new Ajax.Request(this.url, {
      method: 'get',
      parameters: { 'timestamp' : this.timestamp },
      onSuccess: function(transport) {
        // handle the server response
        var response = transport.responseText.evalJSON();
        this.comet.timestamp = response['timestamp'];
        this.comet.handleResponse(response);
        this.comet.noerror = true;
      },
      onComplete: function(transport) {
        // send a new ajax request when this request is finished
        if (!this.comet.noerror)
          // if a connection problem occurs, try to reconnect each 5 seconds
          setTimeout(function(){ comet.connect() }, 5000);
        else
          this.comet.connect();
        this.comet.noerror = false;
      }
    });
    this.ajax.comet = this;
  },

  disconnect: function()
  {
  },

  handleResponse: function(response)
  {

    $('content').innerHTML += '<div>' + response['msg'] + '</div>';

  },

  doRequest: function(request)
  {
    new Ajax.Request(this.url, {
      method: 'get',
      parameters: { 'msg' : request }
    });
  }
}
var comet = new Comet();
comet.connect();

</script>

<?php include_partial('form', array('form' => $form)) ?>
