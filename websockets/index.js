const httpServer = require("http").createServer();
const io = require("socket.io")(httpServer, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"],
    credentials: true,
  },
});
httpServer.listen(8080, () => {
  console.log("listening on *:8080");
});

io.on("connection", (socket) => {
  console.log(`client ${socket.id} has connected`);

  socket.on("echo", (message) => {
    socket.emit("echo", message);
    console.log(`client ${socket.id} sent: ${message}`);
  });

  socket.on('connectUser', ({ userId }) => {
    console.log(`client ${socket.id} connected as user_${userId}`);
    socket.join(`user_${userId}`);
  });

  socket.on("sendmoney", (data) => {
    console.log("Received sendMoney event:", data);
    console.log("vcard:", data.transactionData.vcard);
    // Emita a notificação para o usuário específico usando o ID do usuário
    io.to(`user_${data.transactionData.vcard}`).emit("notification", "Você recebeu " + data.transactionData.value  +  "€");
  });

  socket.on("sendmoneyTransaction", (data) => {
    console.log("Received sendMoney event:", data);
    console.log("vcard:", data.transactionData.vcard);
    
    // Emita a notificação para o usuário específico usando o ID do usuário
    io.to(`user_${data.transactionData.pair_vcard}`).emit("notification", "Você recebeu " + data.transactionData.value  +  "€" + " de " + data.transactionData.vcard);
  });

  socket.on("Block", (data) => {
    console.log("Received sendMoney event:", data);
    console.log("data:", data.data);
    // Emita a notificação para o usuário específico usando o ID do usuário
    if(data.data === 0){
      io.to(`user_${data.userId}`).emit("notification", "Você foi bloqueado");
    }
    else if(data.data === 1){
      io.to(`user_${data.userId}`).emit("notification", "Você foi desbloqueado");
    }
  });
});

