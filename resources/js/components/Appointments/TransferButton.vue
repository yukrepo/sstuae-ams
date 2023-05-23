<template>
    <button class="btn btn-purple" @click="TransferInInsurance(appointment.appointment_code)"> 
        <i class="fas fa-exchange-alt"></i> 
    </button>
</template>
<script>
export default {
    props: {

    },
    methods: {
        TransferInInsurance(apid) {
            swal.fire({
                title: 'Are you sure?',
                text: "You want to transfer this appointment in insurance?",
                footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#C70039',
                cancelButtonColor: '#0DC957',
                confirmButtonText: 'Yes, Do it!',
                cancelButtonText: 'Not Now',
                showLoaderOnConfirm: true,
                preConfirm: (appcode) => {
                    return axios.post('/api/transfer-appointment-in-invoice', {
                        appointment_code:apid,
                        invoice_number:this.course.primary_invoice
                    })
                    .then(response => {
                        //console.log(response.data.status);
                        if(response.data.status == 'error') {
                            throw new Error(response.data.message);
                        } else {
                            swal.fire('Approved!', 'Invoice has been approved.', 'success');
                            this.showCourse();
                        }
                    })
                    .catch(error => {
                        swal.showValidationMessage(`${error}`)
                    })
                },
            });
        },
        checkTransferStatus(tid, iid, inv) { console.log(inv);
            if(iid == 1) {
                return false;
            } else {
                let t1, t2;
                t2 = this.appointments.filter((apt) => { return (apt.treatment_id == tid && apt.invoice == inv) });
                t1 = this.treatments.filter((treat) => { return (treat.id == tid) })
                t1 = parseInt((t1[0].approved)?t1[0].days:t1[0].days)
                t2 = parseInt(t2.length);
                // console.log(t1-t2)
                if(t2 > t1) {
                    return false
                } else {
                    return true
                }
            }
        },
    }
}
</script>