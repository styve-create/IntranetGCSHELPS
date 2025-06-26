    <div class="card-body p-4 bg-white border rounded mt-3 certificados" id="certificados">
                    <h6>Documentos disponibles</h6>
                    <form id="form-certificados" method="POST" action="generar_certificados.php">
                        <input type="hidden" name="documento" value="<?= htmlspecialchars($trabajador['numero_documento']?? '') ?>">
                        <label><input type="checkbox" name="certificados[]" value="Certificacion_ACTIVO_GCS.docx"> Certificación ACTIVO GCS</label><br>
                        <label><input type="checkbox" name="certificados[]" value="Certificacion_RETIRO_GCS.docx"> Certificación RETIRO GCS</label><br>
                        <button type="submit" class="btn btn-primary btn-sm mt-2"><i class="bi bi-download"></i> Descargar certificados</button>
                    </form>
                    <div id="status-certificados" class="mt-2"></div>
                </div>