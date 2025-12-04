<!DOCTYPE html>
<html>
<head>
  <title>Real-Time Voice to Text (Browser Speech API)</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    #text { 
        white-space: pre-wrap; 
        background: #eee; 
        padding: 15px; 
        border-radius: 10px; 
        margin-top: 20px; 
        font-size: 18px; 
    }
  </style>
</head>
<body>

<h2>🎤 Real-Time Voice to Text (No API Key Needed)</h2>

<button id="startBtn">Start</button>
<button id="stopBtn" disabled>Stop</button>

<div id="text">Say something…</div>

<script>
let recognition;

if ("webkitSpeechRecognition" in window) {
    recognition = new webkitSpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = "en-US";

    recognition.onresult = function(event) {
        let output = "";
        for (let i = 0; i < event.results.length; i++) {
            output += event.results[i][0].transcript + " ";
        }
        document.getElementById("text").textContent = output;
    };
} else {
    alert("SpeechRecognition API not supported in this browser.");
}

document.getElementById("startBtn").onclick = () => {
    recognition.start();
    document.getElementById("startBtn").disabled = true;
    document.getElementById("stopBtn").disabled = false;
};

document.getElementById("stopBtn").onclick = () => {
    recognition.stop();
    document.getElementById("startBtn").disabled = false;
    document.getElementById("stopBtn").disabled = true;
};
</script>

</body>
</html>
