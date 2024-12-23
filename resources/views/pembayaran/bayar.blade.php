<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Modal untuk Pembayaran -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Total Akhir: <strong id="final-price-modal">Rp 0</strong></p>
                <div class="mb-3">
                    <label for="amount-paid" class="form-label">Nominal yang Dibayarkan:</label>
                    <input type="text" id="amount-paid" class="form-control" placeholder="Masukkan jumlah bayar" />
                    <input type="hidden" id="totalBayar" />
                </div>
                <p>Kembalian: <strong id="change-amount">Rp 0</strong><input type="hidden" id="kembalian"
                        name="kembalian" /></p>

                <div class="mb-3">
                    <label class="form-label">Metode Pembayaran:</label><br />
                    <input type="radio" id="payment-cash" name="payment-method" value="cash" checked />
                    <label for="payment-cash">Cash</label>
                </div>

                <input type="hidden" id="pembelian-id" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirm-payment">Konfirmasi Pembayaran</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Memperbesar modal */
    .modal-dialog {
        max-width: 800px;
    }

    .form-control {
        font-size: 1.5rem;
        padding: 1rem;
    }

    .modal-content {
        background-color: #f8f9fa;
        border-radius: 10px;
    }

    .modal-header,
    .modal-footer {
        background-color: silver;
        color: white;
    }
</style>

<script>
    $(document).ready(function() {
        function numberWithDots(x) {
            // return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return new Intl.NumberFormat('id-ID').format(x);
        }

        $('#pay-btn').click(function() {
            const finalPrice = parseFloat($('#final-price').text().replace('Rp ', '').replace(/\./g, '')
                .replace(',', '.'));
            $('#final-price-modal').text(`Rp ${numberWithDots(finalPrice)}`);
            $('#amount-paid').val('');
            $('#change-amount').text('Rp 0');
            $('#confirm-payment').prop('disabled', true);
            $('#paymentModal').modal('show');
        });

        $('#amount-paid').on('input', function() {
            let amountPaid = $(this).val().replace(/\D/g, '');
            amountPaid = parseFloat(amountPaid || '0');
            $('#totalBayar').val(amountPaid);

            $(this).val(numberWithDots(amountPaid));

            const finalPrice = parseFloat($('#final-price').text().replace('Rp ', '').replace(/\./g, '')
                .replace(',', '.'));
            const change = amountPaid - finalPrice;
            $('#kembalian').val(change);
            $('#change-amount').text(`Rp ${numberWithDots(change < 0 ? 0 : change)}`);
            $('#confirm-payment').prop('disabled', amountPaid < finalPrice);
        });

        function bayar() {
            const amountPaid = parseFloat($('#totalBayar').val()) || 0;
            const finalPrice = parseFloat($('#final-price').text().replace('Rp ', '').replace(/\./g, '')
                .replace(',', '.'));
            const paymentMethod = $("input[name='payment-method']:checked").val();
            const pembelianId = $('#pembelian-id').val();

            $.ajax({
                url: '/pembayaran',
                method: 'POST',
                data: {
                    pembelian_id: pembelianId,
                    total_pembayaran: amountPaid,
                    metode_pembayaran: paymentMethod,
                    pajak: finalPrice - amountPaid,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Berhasil!',
                        text: response.message,
                    }).then(() => {
                        cetakStruk(pembelianId);
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Gagal',
                        text: xhr.responseJSON.message,
                    });
                }
            });
        }

        function cetakStruk(pembelianId) {
            const kembalian = $('#kembalian').val();
            const totalBayar = $('#totalBayar').val();
            $.ajax({
                url: `{{ url('/pembayaran/print-receipt') }}/${pembelianId}?change=${kembalian}&amountPaid=${totalBayar}`,
                method: 'GET',
                success: function(printResponse) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Struk Berhasil Dicetak!',
                        text: printResponse.message,
                    });
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mencetak Struk',
                        text: xhr.responseJSON ? xhr.responseJSON.message :
                            'Terjadi kesalahan saat mencetak struk.',
                    });
                }
            });
        }

        $('#confirm-payment').click(function() {
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: 'Apakah Anda yakin ingin melakukan pembayaran ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Konfirmasi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    bayar();
                }
            });
        });
    });
</script>
