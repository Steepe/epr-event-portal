// Please see documentation at https://docs.microsoft.com/aspnet/core/client-side/bundling-and-minification
// for details on configuring this project to bundle and minify static web assets.

// Write your JavaScript code.

const connection = new signalR.HubConnectionBuilder()
    //.withUrl("https://097d-102-91-5-88.ngrok.io/creyatifchat")
    .withUrl("https://hwsrv-1014519.hostwindsdns.com/creyatifchat")
    //.withUrl("https://localhost:7288/creyatifchat")
    .withAutomaticReconnect([0, 800, 1000, 1200, 1400, 1600, 1800, 2000, 2100])
    .configureLogging(signalR.LogLevel.Trace)
    .build();

// user authorization
let userAuthIdentifier = '';
let userGroupIdentifier = '';

async function start() {
    try {
        await connection.start();
        console.log("SignalR Connected.");

        // call on reconnect
        if (userAuthIdentifier) {
            connection.invoke("AuthorizationHandShake", userAuthIdentifier).catch(function (err) {
                return console.error(err.toString());
            });
        }
    } catch (err) {
        console.log(err);
        setTimeout(start, 5000);
    }
};

connection.onreconnecting((error) => {
    //disableUi(true);
//console.log('hahahaha')\
    showToast('connection lost')
    //alert('Reconnecting...');
});

connection.onreconnected((connectionId) => {
   // disableUi(false);
    //console.log('hehehehe');
    showToast('connection restored')

    //alert('Reconnected!');
});

connection.onclose(async () => {
    await start();

    if (userAuthIdentifier) {
        LoginAuthHandShake(userAuthIdentifier);
    }

    if (userAuthIdentifier && userGroupIdentifier) {
        UserGroupAssocHandShake(userAuthIdentifier, userGroupIdentifier);
    }
});

// Start the connection.
start();

//
// +++++++++++++++++++++
// Section for Broadcast
// +++++++++++++++++++++
//

// NOTE: this does not need to be changed
// NOTE: only modify as needed
connection.on("BroadcastMessage", (message) => {


    // li.textContent = `${message}`;
    // document.getElementById("messageList").appendChild(li);

    if (userAuthIdentifier) {
        LoginAuthHandShake(userAuthIdentifier);
    }

    if (userAuthIdentifier && userGroupIdentifier) {
        UserGroupAssocHandShake(userAuthIdentifier, userGroupIdentifier);
    }
});


// send broad cast message
function SendBroadMessage(broadMessage) {
    if (broadMessage) {
        //alert(broadMessage);
        connection.invoke("SendBroadMessage", broadMessage).catch(function (err) {
            return console.error(err.toString());
        });
    }
}

//
// ++++++++++++++++++++++
// Section for group chat
// ++++++++++++++++++++++
//

// NOTE: this can be modified and ignored
connection.on("GroupShake", (message) => {
    /*    const li = document.createElement("li");
        li.textContent = `Group association hand shake ${message}`;
        document.getElementById("groupessageList").appendChild(li);*/

    // consider if valid
    if (userAuthIdentifier) {
        connection.invoke("AuthorizationHandShake", userAuthIdentifier).catch(function (err) {
            return console.error(err.toString());
        });
    }
});

// NOTE: this does not need to be changed
// NOTE: only modify as needed
connection.on("GroupMessage", (message) => {
    //alert(message);
    const obj = JSON.parse(message);
    //const baba =
    //console.log(obj);
    //return false;

    const li = document.createElement("li");
    li.classList.add('clearfix');

    const div1 = document.createElement("div");
    div1.classList.add('message-data');

    const div2 = document.createElement("div");
    div2.classList.add('message', 'other-message');
    div2.append(obj.chat_message);

    const span1 = document.createElement("span");
    span1.classList.add('chatter-name');
    span1.append(obj.fullname);

    const span2 = document.createElement("span");
    span2.classList.add('message-data-time', 'float-right');
    span2.append(obj.chat_time);

    const span3 = document.createElement("span");
    span3.classList.add('epr-text-purple');
    span3.innerHTML += " <a class=\"private-chat\" onclick=\"open_private_chat_window('" + obj.useremail + "')\" title=\"Private Chat\"><i class=\"bi bi-chat-square-dots\"></i></a>";

    //
    /*    if(userAuthIdentifier === obj.attendee_id.toString())
        {
            const span3 = document.createElement("span");
            span3.classList.add('epr-text-purple');
        }else
        {
            const span3 = document.createElement("span");
            span3.classList.add('epr-text-purple');
            span3.innerHTML += " <a class=\"private-chat\" onclick=\"open_private_chat_modal('"+obj.useremail+"')\" title=\"Private Chat\"><i class=\"bi bi-chat-square-dots\"></i></a>";
        }*/

    div1.appendChild(span1);
    div1.appendChild(span2);
    div1.appendChild(span3);
    li.append(div1, div2);

    document.getElementById("groupmessageList").appendChild(li);

    const el = document.getElementById('chat-feed');
    // id of the chat container ---------- ^^^
    //console.log(el)
    if (el) {
        el.scrollTop = el.scrollHeight;
    }

    // consider if valid
    if (userAuthIdentifier) {
        connection.invoke("AuthorizationHandShake", userAuthIdentifier).catch(function (err) {
            return console.error(err.toString());
        });
    }
});

// group association hand shake
function UserGroupAssocHandShake(userIdentifier, groupIdentifier) {
    // perform hand shake
    LoginAuthHandShake(userIdentifier);

    if (groupIdentifier) {
        userGroupIdentifier = groupIdentifier;
        connection.invoke("AssociationHandShake", groupIdentifier).catch(function (err) {
            return console.error(err.toString());
        });
        // event.preventDefault();
    }
}

// send group message
function SendGroupMessage(userIdentifier, groupIdentifier, groupMessage) {
    //alert(userIdentifier);
    //alert(groupIdentifier);
    //alert(groupMessage);
    //console.log(groupMessage);

    //
    if (connection.open)

        // perform hand shake
        LoginAuthHandShake(userIdentifier);

    if (groupIdentifier) {
        connection.invoke("SendGroupMessage", groupIdentifier, groupMessage).catch(function (err) {
            alert(err);
            return console.error(err.toString());
        });
    }
}

//
// ++++++++++++++++++++++++
// Section for Private chat
// ++++++++++++++++++++++++
//

// NOTE: this can be modified and ignored
connection.on("HandShake", (message) => {
    /*const li = document.createElement("li");
    li.textContent = `Authorization ${message}`;
    document.getElementById("messageList").appendChild(li);*/
});

// NOTE: this does not need to be changed
// NOTE: only modify as needed
connection.on("PrivateMessage", (message) => {
    const obj = JSON.parse(message);
    //const baba =
    //console.log(obj);
    //return false;
    // alert(message);

    open_private_message_window(obj);

    /*   const li = document.createElement("li");
       li.classList.add('clearfix');

       const div1 = document.createElement("div");
       div1.classList.add('message-data');

       const div2 = document.createElement("div");
       div2.classList.add('message', 'other-message');
       div2.append(obj.chat_message);

       const span1 = document.createElement("span");
       span1.classList.add('chatter-name');
       span1.append(obj.fullname);

       const span2 = document.createElement("span");
       span2.classList.add('message-data-time', 'float-right');
       span2.append(obj.chat_time);

       //
       /!*    if(userAuthIdentifier === obj.attendee_id.toString())
           {
               const span3 = document.createElement("span");
               span3.classList.add('epr-text-purple');
           }else
           {
               const span3 = document.createElement("span");
               span3.classList.add('epr-text-purple');
               span3.innerHTML += " <a class=\"private-chat\" onclick=\"open_private_chat_modal('"+obj.useremail+"')\" title=\"Private Chat\"><i class=\"bi bi-chat-square-dots\"></i></a>";
           }*!/

       div1.appendChild(span1);
       div1.appendChild(span2);
       li.append(div1, div2);

       document.getElementById("messageList").append(li);

       const el = document.getElementById('chat-feed');
       // id of the chat container ---------- ^^^
       //console.log(el)
       if (el) {
           el.scrollTop = el.scrollHeight;
       }
       */

    // consider if valid
    if (userAuthIdentifier) {
        connection.invoke("AuthorizationHandShake", userAuthIdentifier).catch(function (err) {
            return console.error(err.toString());
        });
    }
});

// authorization hand shake
function LoginAuthHandShake(userIdentifier) {
    if (userIdentifier) {
        connection.invoke("AuthorizationHandShake", userIdentifier).catch(function (err) {
            return console.error(err.toString());
        });

        userAuthIdentifier = userIdentifier;
    }
}

// send private message
function SendPrivateMessage(userIdentifier, recipientIdentifier, privateMessage) {
    //alert(userIdentifier);
    // perform hand shake
    LoginAuthHandShake(userIdentifier);

    if (recipientIdentifier) {
        //console.log(privateMessage);
        connection.invoke("SendPrivateMessage", recipientIdentifier, privateMessage).catch(function (err) {
            console.log(err);
            return console.error(err.toString());
        });
    }
}

function open_private_message_window(message_object) {
    let chat_identifier = message_object.chat_identifier;
    let sender_email = message_object.sender_email;

    let features = "menubar=no,toolbar=no,scrollbars=yes,resizable=no,location=no,left=500,width=300,height=500";
    let private_window = window.open("", chat_identifier, features);
    let iframe_src = "<iframe id='private_window' src='https://innovate.creyatif/chat?p=1&recipient_email=" + sender_email + "' style='position:absolute;top:0;left:0;width:100%; height:100%; border: none; border-radius: 10px; overflow:hidden !important;'></iframe>";
    private_window.document.body.innerHTML = iframe_src;
    const iframe = private_window.document.getElementById('private_window');
    //alert(iframe);
    private_window.addEventListener('load', function () {
        //alert("dede");
        const messageList = iframe.contentWindow.document.getElementById('messageList');
        //alert(list);

        const li = document.createElement("li");
        li.classList.add('clearfix');

        const div1 = document.createElement("div");
        div1.classList.add('message-data');

        const div2 = document.createElement("div");
        div2.classList.add('message', 'other-message');
        div2.append(message_object.chat_message);

        const span1 = document.createElement("span");
        span1.classList.add('chatter-name');
        span1.append(message_object.fullname);

        const span2 = document.createElement("span");
        span2.classList.add('message-data-time', 'float-right');
        span2.append(message_object.chat_time);

        div1.appendChild(span1);
        div1.appendChild(span2);
        li.append(div1, div2);

        messageList.append(li);

        const el = document.getElementById('chat-feed');
        // id of the chat container ---------- ^^^
        //console.log(el)
        if (el) {
            el.scrollTop = el.scrollHeight;
        }
    });
}

function showToast(message) {
    iqwerty.toast.toast(message, {
        style: {
            main: {
                'background': '#9D0F82',
                'color': '#FFFFFF',
            },
        },
        settings: {
            duration: 7000,
        },
    });
}
