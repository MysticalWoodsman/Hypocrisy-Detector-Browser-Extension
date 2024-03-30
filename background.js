// Background script (background.js) for the extension

// Message listener to receive data from the extension frontend
chrome.runtime.onMessage.addListener(function(message, sender, sendResponse) {
    if (message.dataToServer) {
        // Send data to server using AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "https://example.com/write_data.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    console.log("Data sent to server successfully");
                    // Optionally, process response from server if needed
                    sendResponse({ success: true });
                } else {
                    console.error("Error sending data to server");
                    sendResponse({ success: false });
                }
            }
        };
        xhr.send("data=" + encodeURIComponent(message.dataToServer));
        // Return true to indicate that sendResponse will be called asynchronously
        return true;
    }
});
