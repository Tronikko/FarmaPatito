<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Farma-Patito</title>
  <style>

body { 
  font-family: Arial, sans-serif; 
  background: linear-gradient(270deg, rgb(0, 42, 255), rgb(0, 150, 255), rgb(0, 255, 200));
  background-size: 600% 600%;
  animation: fondoMovimiento 10s infinite alternate ease-in-out;
  display: flex; 
  flex-direction: column; 
  align-items: center; 
  padding: 20px; 
  position: relative;
  margin: 0;
  height: 100vh;
}

@keyframes fondoMovimiento {
  0% { background-position: 0% 50%; }
  100% { background-position: 100% 50%; }
}


    .container { 
      width: 90%; 
      text-align: center; 
      margin-bottom: 40px;
    }
    .productos-container { 
      display: flex; 
      justify-content: center; 
      align-items: center; 
      gap: 30px; 
      flex-wrap: wrap;
    }
    .producto { 
      text-align: center; 
      padding: 10px;  /* Se reduce el padding */
      background-color: #fff; 
      border-radius: 10px; 
      box-shadow: 0px 4px 8px #aaa; 
      width: 250px; 
      margin-bottom: 10px;
    }
    .producto img { 
      width: 150px; 
      height: 150px; 
      object-fit: contain;
      border-radius: 10px; 
      margin-bottom: 5px;
    }
    /* Estilo para el input de cantidad: reducido y con fondo azul claro */
    .producto input[type="number"] {
      width: 50px;
      height: 30px;
      padding: 2px;
      font-size: 14px;
      margin-bottom: 5px;
      background-color: #e0f0ff;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .boton { 
      background-color: green; 
      color: white; 
      padding: 8px 10px;  /* Se reduce un poco el padding */
      border: none; 
      cursor: pointer; 
      border-radius: 8px; 
      font-size: 16px; 
      margin-top: 5px; 
    }
    .boton:hover { 
      background-color: darkgreen; 
    }
    .boton-navegacion { 
      background-color: orange; 
      color: white; 
      padding: 12px; 
      border: none; 
      cursor: pointer; 
      border-radius: 8px; 
      font-size: 16px; 
      margin: 10px;
    }
    .boton-navegacion:hover { 
      background-color: darkorange; 
    }
    .boton-carrito { 
      background-color: orange; 
      color: white; 
      padding: 12px; 
      border: none; 
      cursor: pointer; 
      border-radius: 8px; 
      font-size: 16px; 
      position: fixed; 
      top: 10px; 
      right: 10px;
      z-index: 1000;
    }
    .boton-carrito:hover { 
      background-color: darkorange; 
    }
    /* Contador de productos en el botón del carrito */
    #cart-count {
      background: blue;
      color: white;
      border-radius: 50%;
      padding: 1px 4px;
      margin-left: 5px;
      font-size: 12px;
    }
    .boton-regresar {
      background-color: gray; 
      color: white; 
      padding: 12px; 
      border: none; 
      cursor: pointer; 
      border-radius: 8px; 
      font-size: 16px; 
      margin-top: 20px;
    }
    .boton-regresar:hover {
      background-color: darkblue;
    }
    .carrito-contenedor { 
      width: 300px; 
      background-color: #fff; 
      padding: 20px; 
      border-left: 2px solid #ddd; 
      position: fixed; 
      right: 0; 
      top: 0; 
      height: 100vh; 
      box-shadow: -3px 0px 10px #aaa; 
      overflow-y: auto; 
      transform: translateX(100%); 
      transition: transform 0.3s ease-in-out; 
      z-index: 999;
    }
    .carrito-contenedor.mostrar { 
      transform: translateX(0); 
    }
    /* Estilo para notificación (toast) */
    #notification {
      position: fixed;
      top: 50px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #333;
      color: #fff;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 16px;
      opacity: 0;
      transition: opacity 0.5s ease-in-out;
      z-index: 1001;
    }

    h2 {
  font-size: 40px;
  font-weight: bold;
  font-family: 'Courier New', monospace;
  color: #4CAF50; /* Un verde vibrante y llamativo */
  text-shadow: 3px 3px 5px rgba(0, 0, 0, 0.3);
  letter-spacing: 2px;
  border-bottom: 3px solid #388E3C; /* Línea decorativa */
  display: inline-block;
  padding-bottom: 5px;
  animation: vibrar 0.1s infinite alternate;
}

@keyframes vibrar {
  from {
    transform: translateX(-2px);
  }
  to {
    transform: translateX(2px);
  }

  .container img {
    height: 60px; /* Ajusta el tamaño */
    width: auto;
    border-radius: 10px; /* Si quieres bordes redondeados */
}


}


  </style>
  <script>
    // Se definen los 10 productos, cada uno con su stock registrado
    let productos = [
      { nombre: "Amoxicilina", precio: 60, imagen: "imagenes/Amoxicilina.jpg", stock: 10 },
      { nombre: "Diclofenaco", precio: 45, imagen: "imagenes/Diclofenaco.jpg", stock: 8 },
      { nombre: "Ibuprofeno", precio: 50, imagen: "imagenes/Ibuprofeno.jpg", stock: 15 },
      { nombre: "Paracetamol", precio: 40, imagen: "imagenes/Paracetamol.jpg", stock: 12 },
      { nombre: "Loratadina", precio: 35, imagen: "imagenes/Loratadina.jpg", stock: 7 },
      { nombre: "Metformina", precio: 40, imagen: "imagenes/Metformina.jpg", stock: 10 },
      { nombre: "Omeprazol", precio: 55, imagen: "imagenes/Omeprazol.jpg", stock: 10 },
      { nombre: "Ranitidina", precio: 50, imagen: "imagenes/Ranitidina.jpg", stock: 8 },
      { nombre: "Salbutamol", precio: 65, imagen: "imagenes/Salbutamol.jpg", stock: 5 },
      { nombre: "Vitamina C", precio: 30, imagen: "imagenes/Vitamina_C.jpg", stock: 20 }
    ];

    // Objeto para acumular productos en el carrito
    let carrito = {};

    // Variables para el carrusel de productos
    let indiceInicio = 0;
    const cantidadVisible = 3;

    // Función para mostrar tres productos a la vez
    function mostrarProductos() {
      let productosContainer = document.getElementById("productos-container");
      productosContainer.innerHTML = "";
      for (let i = indiceInicio; i < indiceInicio + cantidadVisible && i < productos.length; i++) {
        let producto = productos[i];
        productosContainer.innerHTML += `
          <div class="producto">
            <img src="${producto.imagen}" alt="${producto.nombre}">
            <p><strong>${producto.nombre}</strong></p>
            <p>Precio: $${producto.precio}.00</p>
            <p>Stock: ${producto.stock}</p>
            <input type="number" id="cantidad_${producto.nombre}" min="1" value="1">
            <button class="boton" onclick="agregarAlCarrito('${producto.nombre}', ${producto.precio})">Agregar</button>
          </div>
        `;
      }
    }

    function avanzar() {
      if (indiceInicio + cantidadVisible < productos.length) {
        indiceInicio += cantidadVisible;
        mostrarProductos();
      }
    }

    function retroceder() {
      if (indiceInicio - cantidadVisible >= 0) {
        indiceInicio -= cantidadVisible;
        mostrarProductos();
      }
    }

    // Función para agregar un producto al carrito
    // Verifica que la cantidad acumulada no supere el stock disponible
    function agregarAlCarrito(nombre, precio) {
      let cantidadInput = document.getElementById("cantidad_" + nombre);
      let cantidad = parseInt(cantidadInput.value) || 1;
      let productoObj = productos.find(p => p.nombre === nombre);
      let currentInCart = carrito[nombre] ? carrito[nombre].cantidad : 0;

      if (currentInCart + cantidad > productoObj.stock) {
        showNotification("Stock insuficiente para " + nombre + ". Disponible: " + (productoObj.stock - currentInCart));
        return;
      }

      if (carrito[nombre]) {
        carrito[nombre].cantidad += cantidad;
      } else {
        carrito[nombre] = { precio: precio, cantidad: cantidad };
      }
      mostrarCarrito();
      updateCartCount();
      showNotification("Producto agregado");
    }

    // Actualiza el contenido del carrito
    function mostrarCarrito() {
      let carritoContainer = document.getElementById("carrito");
      carritoContainer.innerHTML = "<h3>Carrito de Compras</h3><ul>";
      let total = 0;
      for (let prod in carrito) {
        let item = carrito[prod];
        carritoContainer.innerHTML += `<li>${prod} - ${item.cantidad} pieza(s) - $${(item.precio * item.cantidad).toFixed(2)}</li>`;
        total += item.precio * item.cantidad;
      }
      carritoContainer.innerHTML += `</ul><h4>Total: $${total.toFixed(2)}</h4>`;
    }

    // Actualiza el contador global mostrado en el botón del carrito
    function updateCartCount() {
      let totalCount = 0;
      for (let prod in carrito) {
        totalCount += carrito[prod].cantidad;
      }
      document.getElementById("cart-count").innerText = totalCount;
    }

    // Muestra una notificación temporal (toast)
    function showNotification(message) {
      let notif = document.getElementById("notification");
      notif.innerText = message;
      notif.style.opacity = 1;
      setTimeout(() => { notif.style.opacity = 0; }, 2000);
    }

    // Alterna la visibilidad del carrito
    function mostrarOcultarCarrito() {
      document.getElementById("carrito-contenedor").classList.toggle("mostrar");
    }

    // Función para regresar al index.php
    function regresarAlInicio() {
      window.location.href = "index.php";
    }

    // Oculta el carrito si se hace clic fuera de él o del botón de carrito
    window.onclick = function(event) {
      if (!event.target.closest('.carrito-contenedor') && !event.target.closest('.boton-carrito')) {
        document.getElementById("carrito-contenedor").classList.remove("mostrar");
      }
    };

    window.onload = function() {
      mostrarProductos();
      updateCartCount();
    };

    
  </script>
</head>
<body>
  <button class="boton-carrito" onclick="mostrarOcultarCarrito()">🛒 Carrito <span id="cart-count">0</span></button>
  <div class="titulo-container">
    <h2>Farma-Patito</h2>
    <img src="imagenes/pato.jpg" alt="Logo de Farma-Patito">
</div>


  <div class="productos-container" id="productos-container"></div>
<div>
  <button class="boton-navegacion" onclick="retroceder()">⬅ Atrás</button>
  <button class="boton-navegacion" onclick="avanzar()">➡ Adelante</button>
</div>
<button class="boton-regresar" onclick="regresarAlInicio()">Regresar</button>

<div id="carrito-contenedor" class="carrito-contenedor">
  <div id="carrito">
    <h3>Carrito de Compras</h3>
    <p>Aún no hay productos en el carrito.</p>

    <!-- Campo para ingresar correo -->
    <input type="email" id="correoDestinatario" placeholder="Ingresa tu correo aquí" required>
    
    <!-- Botón para enviar el ticket por correo -->
    <button id="enviar-ticket" class="boton" onclick="enviarPorCorreo()">Enviar Ticket por Correo</button>
  </div>

  <button id="ver-pdf" class="boton" onclick="generarTicketPDF()">Ver Ticket en PDF</button>
  
  <!-- Botón de Finalizar Compra dentro del carrito -->
  <button id="finalizar-compra" class="boton" onclick="finalizarCompra()">Finalizar Compra</button>
</div>

<button id="btnEnviarCorreo">Enviar por correo</button>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
function finalizarCompra() {
    let fechaHora = new Date().toISOString().slice(0, 19).replace("T", " ");
    let usuario = "ClientePrueba"; 
    let productosLista = [];
    let total = 0;

    for (let prod in carrito) {
        let item = carrito[prod];
        productosLista.push(`${prod} - ${item.cantidad} pieza(s) - $${(item.precio * item.cantidad).toFixed(2)}`);
        total += item.precio * item.cantidad;
    }

    let productosTexto = productosLista.join("; ");

    // ✅ Generar el ticket **antes** de eliminar el carrito
    generarTicket();

    fetch("guardar_ticket.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            fecha: fechaHora,
            usuario: usuario,
            productos: productosTexto,
            total: total.toFixed(2)
        })
    })
    .then(response => response.text())
    .then(data => {
        console.log("Ticket guardado en BD:", data);
        
        // ✅ Vaciar el carrito después de generar el ticket
        setTimeout(() => {
            carrito = {};
            localStorage.removeItem("carrito");
            mostrarCarrito();
            updateCartCount();
            alert("Compra finalizada. Los productos han sido eliminados del carrito.");
        }, 2000);  // Esperar 2 segundos para evitar que el ticket se vacíe inmediatamente
    })
    .catch(error => console.error("Error al guardar el ticket:", error));
}

    // Función para generar el ticket en pantalla
    function generarTicket() {
        let ticketContainer = document.createElement("div");
        ticketContainer.id = "ticket";
        ticketContainer.style.background = "#fff";
        ticketContainer.style.padding = "20px";
        ticketContainer.style.border = "1px solid #ccc";
        ticketContainer.style.position = "fixed";
        ticketContainer.style.top = "50%";
        ticketContainer.style.left = "50%";
        ticketContainer.style.transform = "translate(-50%, -50%)";
        ticketContainer.style.boxShadow = "0px 4px 8px rgba(0,0,0,0.2)";
        ticketContainer.style.zIndex = "1000";

        let fechaHora = new Date().toLocaleString();
        let ticketHTML = `<h3>Ticket de Compra</h3><p>Fecha y Hora: ${fechaHora}</p><ul>`;

        let total = 0;
        for (let prod in carrito) {
            let item = carrito[prod];
            let productoInfo = productos.find(p => p.nombre === prod);
            ticketHTML += `
                <li style="display: flex; align-items: center;">
                    <img src="${productoInfo.imagen}" alt="${prod}" style="width: 50px; height: 50px; margin-right: 10px;">
                    ${prod} - ${item.cantidad} pieza(s) - $${(item.precio * item.cantidad).toFixed(2)}
                </li>`;
            total += item.precio * item.cantidad;
        }

        ticketHTML += `</ul><h4>Total: $${total.toFixed(2)}</h4>`;
        ticketHTML += `<button onclick="cerrarTicket()">Cerrar</button>`;

        ticketContainer.innerHTML = ticketHTML;
        document.body.appendChild(ticketContainer);
    }

    // Función para generar el ticket en PDF
 function generarTicketPDF() {
    const { jsPDF } = window.jspdf;
    let doc = new jsPDF();
    
    let fechaHora = new Date().toLocaleString();
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text("Ticket de Compra", 20, 20);
    doc.setFontSize(12);
    doc.text(`Fecha y Hora: ${fechaHora}`, 20, 30);

    let yPos = 50;
    let total = 0;
    
    for (let prod in carrito) {
        let item = carrito[prod];
        let productoInfo = productos.find(p => p.nombre === prod);
        
        doc.text(`${prod} - ${item.cantidad} pieza(s) - $${(item.precio * item.cantidad).toFixed(2)}`, 20, yPos);
        yPos += 10;
        total += item.precio * item.cantidad;
    }

    doc.setFont("helvetica", "bold");
    doc.setFontSize(14);
    doc.text(`Total: $${total.toFixed(2)}`, 20, yPos + 10);

    let pdfData = doc.output("blob"); // Genera el PDF como un blob
    
    let formData = new FormData();
    formData.append("ticketPDF", pdfData, "ticket_compra.pdf");

    fetch("enviar_ticket.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => console.log("Correo enviado:", data))
    .catch(error => console.error("Error al enviar el correo:", error));
    
    doc.save("ticket_compra.pdf");
}

    // Función para cerrar el ticket correctamente
function cerrarTicket() {
    let ticket = document.getElementById("ticket");
    if (ticket) {
        document.body.removeChild(ticket);
    }
    
    // Vacía el carrito y actualiza la vista
    carrito = {};
    localStorage.removeItem("carrito");
    mostrarCarrito();
    updateCartCount();
}
</script>

  <!-- Notificación (toast) -->
  <div id="notification"></div>
  <script src="main.js"></script>
</body>
</html>
