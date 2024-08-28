const userId = 2;
const socket = new WebSocket('ws://localhost:9502');

socket.onopen = function() {
    const userId = '12345';
    socket.send(JSON.stringify({
        type: 'auth',
        
    }));
};

socket.onmessage = function(event) {
    const messageData = JSON.parse(event.data);
    const messages = document.getElementById('messages');
    const messageElement = document.createElement('div');

    if (messageData.user_id === userId) {
        messageData.isSelf = true;
    }

    if (messageData.isSelf) {
        messageElement.className = "text-white bg-blue-500 rounded-lg p-2 mb-2 max-w-max ml-auto text-right";
    } else {
        messageElement.className = "text-gray-800 bg-gray-200 rounded-lg p-2 mb-2 max-w-max mr-auto text-left";
    }

    messageElement.innerHTML = `<strong>${messageData.user_id}:</strong> ${messageData.text}`;
    messages.appendChild(messageElement);
    messages.scrollTop = messages.scrollHeight;
};

socket.onclose = function() {
    console.log("Connection closed");
};

socket.onerror = function(error) {
    console.log("WebSocket error: " + error.message);
};

document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);
document.getElementById('msg').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

function sendMessage() {
    const message = document.getElementById('msg').value.trim();
    if (message) {
        socket.send(JSON.stringify({
            from: `1`,
            to: 12,
            message: message
        }));
        document.getElementById('msg').value = '';
    }
}