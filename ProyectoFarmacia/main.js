

// Mostrar productos en pantalla
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

// Funciones de navegación entre productos
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

// Agregar producto al carrito
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

// Mostrar contenido del carrito
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

// Actualizar contador de productos en el botón del carrito
function updateCartCount() {
  let totalCount = 0;
  for (let prod in carrito) {
    totalCount += carrito[prod].cantidad;
  }
  document.getElementById("cart-count").innerText = totalCount;
}

// Muestra una notificación temporal
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

// Regresa a index.php
function regresarAlInicio() {
  window.location.href = "index.php";
}

// Oculta el carrito si se hace clic fuera de él
window.onclick = function(event) {
  if (!event.target.closest('.carrito-contenedor') && !event.target.closest('.boton-carrito')) {
    document.getElementById("carrito-contenedor").classList.remove("mostrar");
  }
};

// ✅ Nueva función para enviar el ticket por correo
function enviarPorCorreo() {
    let correo = document.getElementById("correoDestinatario").value;

    if (!correo) {
        alert("Por favor, ingresa un correo válido.");
        return;
    }

    generarTicketPDF(); // Generar el PDF antes de enviarlo

    setTimeout(() => {  
        let formData = new FormData();
        formData.append("ticketPDF", "ticket_compra.pdf");
        formData.append("correo", correo);  

        fetch("enviar_ticket.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => alert(data.message))
        .catch(error => console.error("Error al enviar el ticket:", error));
    }, 2000);  
}

// Cargar productos y carrito al iniciar
window.onload = function() {
  mostrarProductos();
  updateCartCount();
};