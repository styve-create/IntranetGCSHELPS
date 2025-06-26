<div class="card shadow-sm mb-4">
  <div class="row g-0">

    <!-- Lado izquierdo: foto, nombre y cargo -->
    <div class="col-md-4 bg-light d-flex flex-column align-items-center justify-content-center p-4">
      <div class="position-relative mb-3">
        <img id="profile-img-preview"
             src="<?= $URL . htmlspecialchars($trabajador['foto_perfil']?? '') ?>"
             class="img-fluid rounded-circle shadow anuncioImg cursor-zoom"
             style="width: 170px; height: 170px; object-fit: cover;"
             onclick="ampliarImagen(this)"
             alt="Foto de perfil">
        <label for="upload-photo"
               class="btn btn-light position-absolute bottom-0 end-0 m-2 d-flex align-items-center justify-content-center shadow"
               style="width: 40px; height: 40px; border-radius: 50%; cursor: pointer;">
          <i class="bi bi-camera"></i>
          <input type="file" id="upload-photo" name="foto" class="d-none" accept="image/*">
        </label>
      </div>

      <h5 class="mb-1 text-center"><?= htmlspecialchars($trabajador['nombre_completo']?? '') ?></h5>
      <p class="text-muted text-center"><?= htmlspecialchars($trabajador['cargo_certificado'] ?? '') ?></p>
    </div>

    <!-- Lado derecho: información personal -->
    <div class="col-md-8">
      <div class="card-body">
        <h5 class="mb-3">Información personal</h5>
        <div class="row">
          <div class="col-sm-6 mb-2"><strong>Documento:</strong> <?= htmlspecialchars($trabajador['numero_documento']?? '') ?></div>
          <div class="col-sm-6 mb-2"><strong>Teléfono:</strong> <?= htmlspecialchars($trabajador['celular']?? '') ?></div>
          <div class="col-sm-6 mb-2"><strong>Email:</strong> <?= htmlspecialchars($trabajador['email']?? '') ?></div>
          <div class="col-sm-6 mb-2"><strong>Grupo sanguíneo:</strong> <?= htmlspecialchars($trabajador['grupo_sanguineo']?? '') ?></div>
          <div class="col-sm-12 mb-2"><strong>Dirección:</strong> <?= htmlspecialchars($trabajador['domicilio']?? '') ?></div>
          <div class="col-sm-6 mb-2"><strong>Contacto de emergencia:</strong> <?= htmlspecialchars($trabajador['nombre_contacto_emergencia']?? '') ?></div>
          <div class="col-sm-6 mb-2"><strong>Tel. emergencia:</strong> <?= htmlspecialchars($trabajador['numero_contacto_emergencia']?? '') ?></div>
          <div class="col-sm-6 mb-2"><strong>EPS:</strong> <?= htmlspecialchars($trabajador['eps']?? '') ?></div>
        </div>
      </div>
    </div>

  </div>
</div>