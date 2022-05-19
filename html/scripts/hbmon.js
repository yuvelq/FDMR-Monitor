var sock = null;
var ellog = null;
const conf_groups = [];

window.onload = function() {
  var wsuri;
  conf_id();

  ellog = document.getElementById('log');

  bridge_tbl = document.getElementById('bridge');
  main_tbl = document.getElementById('main');
  lnksys_tbl = document.getElementById('lnksys');
  opb_tbl = document.getElementById('opb');
  statictg_tbl = document.getElementById('statictg');
  tgcount_tbl = document.getElementById('tgcount');
  lsthrd_log_tbl = document.getElementById('lsthrd_log');

  wsuri = (((window.location.protocol === "https:") ? "wss://" : "ws://") + window.location.hostname + ":9000");

  if ("WebSocket" in window) {
    sock = new WebSocket(wsuri);
  } else if ("MozWebSocket" in window) {
    sock = new MozWebSocket(wsuri);
  } else {
    if (ellog != null) {
    log("Browser does not support WebSocket!");}
  }

  if (sock) {
    sock.onopen = function() {
      if (conf_groups.length > 0) {
        sock.send("conf," + conf_groups);
      }
      if (ellog != null) {
        log("Connected to " + wsuri);
      }
    }

    sock.onclose = function(e) {
      if (ellog != null) {
        log("Connection closed (wasClean = " + e.wasClean + ", code = " + e.code + ", reason = '" + e.reason + "')");}
      sock = null;
      for (i = 0; i < conf_groups.length; i++) {
        var group = conf_groups[i];
        if (group == 'bridge') {
          bridge_tbl.innerHTML = "";
        } else if (group == 'main') {
          main_tbl.innerHTML = "";
        } else if (group == 'lnksys') {
          lnksys_tbl.innerHTML = "";
        } else if (group == 'opb') {
          opb_tbl.innerHTML = "";
        } else if (group == 'statictg') {
          statictg_tbl.innerHTML = "";
        } else if (group == 'tgcount') {
          tgcount_tbl.innerHTML = "";
        } else if (group == 'lsthrd_log') {
          lsthrd_log_tbl.innerHTML = "";
        }
      }
    }

    sock.onmessage = function(e) {
      var opcode = e.data.slice(0,1);
      var message = e.data.slice(1);
      if (opcode == "b") {
        Bmsg(message);
      } else if (opcode == "t") {
        Tmsg(message)
      } else if (opcode == "c") {
        Cmsg(message);                   
      } else if (opcode == "i") {
        Imsg(message);         
      } else if (opcode == "o") {
        Omsg(message);         
      } else if (opcode == "s") {
        Smsg(message);
      } else if (opcode == 'h') {
        Hmsg(message);
      } else if (opcode == "l") {
        if (ellog != null) { 
          log(message);}
      } else if (opcode == "q") {
        log(message);
        for (i = 0; i < conf_groups.length; i++) {
          var group = conf_groups[i];
          if (group == "bridge") {
            bridge_tbl.innerHTML = "";
          } else if (group == "main") {
            main_tbl.innerHTML = "";
          } else if (group == "lnksys") {
            masters_tbl.innerHTML = "";
          } else if (group == "opb") {
            opb_tbl.innerHTML = "";
          } else if (group == 'statictg') {
            statictg_tbl.innerHTML = "";
          } else if (group == "tgcount") {
            tgcount_tbl.innerHTML = "";
          } else if (group == 'lsthrd_log') {
            lsthrd_log_tbl.innerHTML = "";
          }
        }
      } else {
        log("Unknown Message Received: " + message);
      }
    }
  }
};


function Bmsg(_msg) { bridge_tbl.innerHTML = _msg; };
function Cmsg(_msg) { lnksys_tbl.innerHTML = _msg; };
function Imsg(_msg) { main_tbl.innerHTML = _msg; };
function Omsg(_msg) { opb_tbl.innerHTML = _msg; };
function Smsg(_msg) { statictg_tbl.innerHTML = _msg; };
function Hmsg(_msg) { lsthrd_log_tbl.innerHTML = _msg; };
function Tmsg(_msg) { tgcount_tbl.innerHTML = _msg; };


function log(_msg) {
  ellog.innerHTML += _msg + '\n';
  ellog.scrollTop = ellog.scrollHeight;};

// Find tables that are present
function conf_id() {
  const groups = ["main", "bridge", "lnksys", "opb", "statictg", "log", "lsthrd_log", "tgcount"];
  const tags = [document.getElementsByTagName("p"), document.getElementsByTagName("pre")]
  for (i = 0; i < tags.length; i++) {
    for (j = 0; j < tags[i].length; j++)
      if (groups.includes(tags[i][j].id)) {
        conf_groups.push(tags[i][j].id);
    }
  }
  console.log(conf_groups)
};
