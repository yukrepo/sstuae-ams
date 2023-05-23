<template>
    <div>
        <div class="container-fluid m-t-10">
            <div class="form-group">
                <div class="button-group">
                    <a
                        class="btn btn-sm btn-primary"
                        target="_blank"
                        :href="
                            '/appointments/print-package-acknowledgement/' +
                            course.course_code
                        "
                        >Print Acknowledgement
                    </a>
                    <button
                        class="btn btn-sm btn-dark"
                        @click="setInvoice()"
                        v-if="!course.invoice"
                    >
                        Set Invoice
                    </button>
                    <span class="float-right">
                        <a
                            class="btn btn-sm btn-dark"
                            :href="
                                '/appointments/course-packages/' + activeYear
                            "
                        >
                            <i class="fas fa-arrow-left"></i> Scheduled Packages
                            List
                        </a>
                    </span>
                </div>
            </div>
            <div class="card full-tabber">
                <div class="card-header p-0 border-bottom-0">
                    <ul
                        class="nav nav-pills"
                        id="pills-tab-desc"
                        role="tablist"
                    >
                        <li class="nav-item">
                            <a
                                class="nav-link active"
                                id="pills-dstab-1"
                                data-toggle="pill"
                                href="#pills-dstab1"
                                role="tab"
                                aria-controls="pills-dstab1"
                                aria-selected="true"
                                >Course Details</a
                            >
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                id="pills-dstab-2"
                                data-toggle="pill"
                                href="#pills-dstab2"
                                role="tab"
                                aria-controls="pills-dstab2"
                                aria-selected="false"
                                >Patient Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                id="pills-dstab-3"
                                data-toggle="pill"
                                href="#pills-dstab3"
                                role="tab"
                                aria-controls="pills-dstab3"
                                aria-selected="true"
                                >Appointment Details</a
                            >
                        </li>
                    </ul>
                </div>
                <div
                    class="tab-content p-0 card-body"
                    id="pills-tabContent-desc"
                >
                    <div
                        class="tab-pane fade show active"
                        id="pills-dstab1"
                        role="tabpanel"
                        aria-labelledby="pills-dstab1-tab"
                    >
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <h5>Course Details</h5>
                                    <table
                                        class="
                                            table table-striped table-bordered
                                        "
                                    >
                                        <thead>
                                            <tr>
                                                <th>Course ID</th>
                                                <th>Remark</th>
                                                <th class="wf-120">
                                                    Started On
                                                </th>
                                                <th class="wf-120">Ends On</th>
                                                <th class="wf-100">
                                                    Appointments
                                                </th>
                                                <th class="text-center wf-100">
                                                    Payment
                                                </th>
                                                <th class="text-center wf-100">
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    {{ course.course_code }}
                                                </td>
                                                <td>
                                                    {{
                                                        course.remark
                                                            ? course.remark
                                                            : "---"
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        course.start_date
                                                            | setdate
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        course.end_date
                                                            | setdate
                                                    }}
                                                    <span
                                                        v-if="
                                                            profile.admin_type_id ==
                                                                1 ||
                                                            profile.admin_type_id ==
                                                                4
                                                        "
                                                    >
                                                        <img
                                                            class="wf-20"
                                                            :src="loaderurl"
                                                            alt="updating"
                                                            v-if="loader == 5"
                                                        />
                                                        <button
                                                            class="
                                                                btn
                                                                btn-sm
                                                                btn-warning
                                                            "
                                                            id="my-custom-input"
                                                            type="button"
                                                            v-else
                                                        >
                                                            <i
                                                                class="
                                                                    fa fa-edit
                                                                "
                                                            ></i>
                                                        </button>
                                                        <vp-date-picker
                                                            locale="en"
                                                            format="YYYY-MM-DD"
                                                            v-model="
                                                                new_end_date
                                                            "
                                                            :auto-submit="true"
                                                            @close="editEndDate"
                                                            element="my-custom-input"
                                                        ></vp-date-picker>
                                                    </span>
                                                </td>
                                                <td>
                                                    {{
                                                        course.appointments_count
                                                            | freeNumber
                                                    }}
                                                </td>
                                                <td align="center">
                                                    <label
                                                        class="
                                                            status-label-full
                                                            bg-danger
                                                        "
                                                        v-if="
                                                            course.pstatus == 1
                                                        "
                                                        >Pending</label
                                                    >
                                                    <label
                                                        class="
                                                            status-label-full
                                                            bg-primary
                                                        "
                                                        v-else-if="
                                                            course.pstatus == 2
                                                        "
                                                        >Partial</label
                                                    >
                                                    <label
                                                        class="
                                                            status-label-full
                                                            bg-success
                                                        "
                                                        v-else-if="
                                                            course.pstatus == 3
                                                        "
                                                        >Paid</label
                                                    >
                                                    <label
                                                        class="
                                                            status-label-full
                                                            bg-warning
                                                            text-dark
                                                        "
                                                        v-else
                                                        >Unknown</label
                                                    >
                                                </td>
                                                <td align="center">
                                                    <label
                                                        class="
                                                            status-label-full
                                                        "
                                                        :class="
                                                            course.status_css
                                                        "
                                                        >{{
                                                            course.status
                                                        }}</label
                                                    >
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-group">
                                    <h5>Package Details</h5>
                                    <table
                                        class="
                                            table table-striped table-bordered
                                        "
                                    >
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Treaments</th>
                                                <th>Consulation</th>
                                                <th>Cost</th>
                                                <th>Timeline</th>
                                                <th>Remark</th>
                                                <th style="width: 50px">
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    {{ therapy_package.name
                                                    }}<br />
                                                    Treatments:
                                                    <b>{{
                                                        therapy_package.treatments
                                                    }}</b
                                                    ><br />
                                                    Consultation:
                                                    <b>{{
                                                        therapy_package.consultation
                                                    }}</b>
                                                </td>
                                                <td>
                                                    <span
                                                        v-for="(
                                                            test, tekey
                                                        ) in therapy_package.treatments_Summary"
                                                        :key="'te-' + tekey"
                                                    >
                                                        {{
                                                            test.count +
                                                            " " +
                                                            test.name
                                                        }}{{
                                                            test.type == 1
                                                                ? " - Payable"
                                                                : " - Free"
                                                        }}<br />
                                                    </span>
                                                </td>

                                                <td>
                                                    <span
                                                        v-for="(
                                                            ctest, cekey
                                                        ) in therapy_package.consultation_Summary"
                                                        :key="'te-' + cekey"
                                                    >
                                                        {{
                                                            ctest.count +
                                                            " " +
                                                            ctest.name
                                                        }}{{
                                                            ctest.type == 1
                                                                ? " - Payable"
                                                                : " - Free"
                                                        }}<br />
                                                    </span>
                                                </td>
                                                <td>
                                                    {{
                                                        therapy_package.cost
                                                            | formatOMR
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        therapy_package.timeline +
                                                        " days"
                                                    }}
                                                </td>
                                                <td>
                                                    {{ therapy_package.remark }}
                                                </td>
                                                <td
                                                    align="center"
                                                    v-if="
                                                        therapy_package.status_id ==
                                                        2
                                                    "
                                                >
                                                    <label
                                                        class="
                                                            status-label
                                                            bg-teal
                                                        "
                                                        ><i
                                                            class="fas fa-check"
                                                        ></i
                                                    ></label>
                                                </td>
                                                <td align="center" v-else>
                                                    <label
                                                        class="
                                                            status-label
                                                            bg-pink
                                                        "
                                                        ><i
                                                            class="fas fa-times"
                                                        ></i
                                                    ></label>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <h5>Invoice Details</h5>
                                    <table
                                        class="
                                            table table-striped table-bordered
                                        "
                                        v-if="course.invoice"
                                    >
                                        <thead>
                                            <tr>
                                                <th>Invoice No</th>
                                                <th>Date</th>
                                                <th>Payment Mode</th>
                                                <th>Sub Total</th>
                                                <th>Net Payable</th>
                                                <th class="wf-175">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    invoice, ikey
                                                ) in invoices"
                                                :key="ikey"
                                            >
                                                <td>
                                                    {{ invoice.invoice_number }}
                                                </td>
                                                <td>
                                                    {{
                                                        invoice.created_at
                                                            | setfulldate
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        invoice.insurance_id ==
                                                        1
                                                            ? "CASH"
                                                            : "Insurance"
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        invoice.amount
                                                            | formatOMR
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        invoice.payable_amount
                                                            | formatOMR
                                                    }}
                                                </td>

                                                <td>
                                                    <router-link
                                                        class="
                                                            btn btn-sm btn-dark
                                                        "
                                                        target="_blank"
                                                        :to="
                                                            '/invoices/print/' +
                                                            invoice.invoice_number
                                                        "
                                                    >
                                                        <i
                                                            class="fas fa-print"
                                                        ></i>
                                                        Print</router-link
                                                    >
                                                    <button
                                                        class="
                                                            btn
                                                            btn-sm
                                                            btn-success
                                                        "
                                                        @click="
                                                            showPayment(
                                                                invoice.invoice_number,
                                                                invoice.payable_amount
                                                            )
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-plus"
                                                        ></i>
                                                        Payment
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="tab-pane fade"
                        id="pills-dstab2"
                        role="tabpanel"
                        aria-labelledby="pills-dstab2-tab"
                    >
                        <div class="form-group">
                            <h5>Patient Details</h5>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>D.O.B. (Age)</th>
                                        <th>Address</th>
                                        <th>Insurance</th>
                                        <th>Identity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span v-if="customer.gender == 1">
                                                <img
                                                    class="img-icon"
                                                    :src="svgurl + 'boy.svg'"
                                                    alt="Male"
                                                />
                                            </span>
                                            <span
                                                v-else-if="customer.gender == 2"
                                            >
                                                <img
                                                    class="img-icon"
                                                    :src="svgurl + 'girl.svg'"
                                                    alt="Female"
                                                />
                                            </span>
                                            {{ customer.username }}
                                        </td>
                                        <td>
                                            {{
                                                customer.user_profile
                                                    ? customer.user_profile
                                                          .first_name
                                                    : ""
                                            }}
                                            {{
                                                customer.user_profile
                                                    ? customer.user_profile
                                                          .last_name
                                                    : ""
                                            }}
                                        </td>
                                        <td>{{ customer.mobile }}</td>
                                        <td>
                                            {{
                                                customer.email
                                                    ? customer.email
                                                    : "--"
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                customer.user_profile
                                                    ? customer.user_profile
                                                          .date_of_birth
                                                    : "" | setdate
                                            }}
                                            {{
                                                customer.user_profile
                                                    ? getAge(
                                                          customer.user_profile
                                                              .date_of_birth
                                                      )
                                                    : ""
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                customer.user_profile
                                                    ? customer.user_profile
                                                          .address
                                                    : ""
                                            }},
                                            {{
                                                customer.user_profile
                                                    ? customer.user_profile.city
                                                    : ""
                                            }}
                                        </td>
                                        <td>
                                            <span v-if="customer.user_document">
                                                <span
                                                    v-show="
                                                        customer.user_document
                                                            .insurance_copy
                                                    "
                                                >
                                                    <img
                                                        class="btn-img"
                                                        v-img:docurl+customer.user_document.insurance_copy
                                                        :src="
                                                            this.docurl +
                                                            customer
                                                                .user_document
                                                                .insurance_copy
                                                        "
                                                    />
                                                </span>
                                                {{
                                                    customer.user_document
                                                        .insurance.name
                                                        | capitalize
                                                }}
                                                ({{
                                                    customer.user_document
                                                        .policy_number
                                                }})
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="customer.user_document">
                                                <span
                                                    v-show="
                                                        customer.user_document
                                                            .identity_copy
                                                    "
                                                >
                                                    <img
                                                        class="btn-img"
                                                        v-img:docurl+customer.user_document.identity_copy
                                                        :src="
                                                            docurl +
                                                            customer
                                                                .user_document
                                                                .identity_copy
                                                        "
                                                    />
                                                </span>
                                                {{
                                                    customer.user_document
                                                        .identity_type.value
                                                        | capitalize
                                                }}
                                                ({{
                                                    customer.user_document
                                                        .verification_number
                                                }})
                                            </span>
                                        </td>
                                        <td>
                                            <button
                                                class="btn btn-sm"
                                                v-if="customer.status"
                                                :class="customer.status.css"
                                            >
                                                {{ customer.status.status }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div
                        class="tab-pane fade"
                        id="pills-dstab3"
                        role="tabpane3"
                        aria-labelledby="pills-dstab3-tab"
                    >
                        <div class="form-group" v-if="course.invoice">
                            <h5 class="m-b-15">
                                Appointment Details
                                <span class="float-right">
                                    <button
                                        class="btn btn-sm btn-dark"
                                        type="button"
                                        @click="makeAppointment"
                                        v-show="newenabled"
                                    >
                                        <i class="fas fa-plus"></i> New
                                        Appointment
                                    </button>
                                </span>
                            </h5>
                            <table
                                id="upcomings"
                                class="
                                    table
                                    table-striped
                                    table-hover
                                    table-bordered
                                "
                            >
                                <thead>
                                    <tr>
                                        <th class="wf-50">SNo</th>
                                        <th>ID</th>
                                        <th>User ID</th>
                                        <th>Patient</th>
                                        <th>Date</th>
                                        <th>Timing</th>
                                        <th>Treatment</th>
                                        <th>Doctor</th>
                                        <th>Invoice</th>
                                        <th class="wf-100">Added On</th>
                                        <th class="wf-120">Status</th>
                                        <th class="wf-120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            appointment, sn
                                        ) in appointments"
                                        :key="appointment.id"
                                    >
                                        <td>{{ ++sn }}</td>
                                        <td>
                                            {{ appointment.appointment_code }}
                                        </td>
                                        <td>{{ appointment.username }}</td>
                                        <td>
                                            {{
                                                appointment.first_name
                                                    | capitalize
                                            }}
                                        </td>
                                        <td>
                                            {{ appointment.date | setdate }}
                                        </td>
                                        <td>{{ appointment.time }}</td>
                                        <td>{{ appointment.reason }}</td>
                                        <td>{{ appointment.doctor }}</td>
                                        <td>
                                            <label
                                                class="
                                                    bg-success
                                                    status-label-full
                                                "
                                                v-if="appointment.invoice"
                                            >
                                                {{ appointment.invoice }}
                                            </label>
                                            <label
                                                class="
                                                    bg-danger
                                                    status-label-full
                                                "
                                                v-else
                                            >
                                                Not invoiced
                                            </label>
                                        </td>
                                        <td>
                                            {{ appointment.created | setdate }}
                                        </td>
                                        <td>
                                            <label
                                                class="status-label-full"
                                                :class="appointment.css"
                                                >{{ appointment.status }}</label
                                            >
                                        </td>
                                        <td>
                                            <span
                                                class="btn-group editbox"
                                                role="group"
                                                v-show="
                                                    appointment.status_id !=
                                                        5 &&
                                                    appointment.status_id != 11
                                                "
                                            >
                                                <button
                                                    type="button"
                                                    class="
                                                        btn btn-warning btn-sm
                                                        dropdown-toggle
                                                    "
                                                    data-toggle="dropdown"
                                                >
                                                    <i
                                                        class="
                                                            fas
                                                            fa-ellipsis-v
                                                        "
                                                    ></i>
                                                </button>
                                                <span
                                                    class="
                                                        dropdown-menu
                                                        dropdown-menu-right
                                                        m-0
                                                        p-0
                                                    "
                                                    aria-labelledby="btnGroupDrop1"
                                                >
                                                    <button
                                                        class="
                                                            dropdown-item
                                                            text-left
                                                            bb-1
                                                            p-2
                                                        "
                                                        @click="
                                                            patientBox(
                                                                appointment
                                                            )
                                                        "
                                                    >
                                                        Change Patient
                                                    </button>
                                                    <button
                                                        class="
                                                            dropdown-item
                                                            text-left
                                                            bb-1
                                                            p-2
                                                        "
                                                        @click="
                                                            doctorBox(
                                                                appointment
                                                            )
                                                        "
                                                    >
                                                        Modify Appointment
                                                    </button>
                                                </span>
                                            </span>
                                            <button
                                                v-show="
                                                    appointment.status_id !=
                                                        5 &&
                                                    appointment.status_id != 11
                                                "
                                                class="
                                                    btn btn-danger
                                                    wf-25
                                                    btn-sm
                                                "
                                                @click="
                                                    cancelAppointment(
                                                        appointment.appointment_code
                                                    )
                                                "
                                            >
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody
                                    v-for="(lth, lthsno) in left_therapies"
                                    :key="'left' + lthsno"
                                >
                                    <tr
                                        v-for="n in lth.remaining"
                                        :key="'rem' + n"
                                    >
                                        <td>{{ n }}</td>
                                        <td>-</td>
                                        <td>{{ customer.username }}</td>
                                        <td>
                                            {{
                                                customer.user_profile
                                                    ? customer.user_profile
                                                          .first_name
                                                    : ""
                                            }}
                                        </td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>{{ lth.name }}</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>
                                            <button
                                                class="
                                                    btn btn-primary
                                                    wf-25
                                                    btn-sm
                                                "
                                                @click="makeAppointment(lth)"
                                            >
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-danger" v-else>
                            Please create invoice first
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="modal fullbarmodal fade"
            id="setInvocieModal"
            data-backdrop="static"
            data-keyboard="false"
            aria-labelledby="setInvocieModalTitle"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form
                        @submit.prevent="
                            editmode ? updateInvoice() : createInvoice()
                        "
                    >
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode">
                                Create Invoice
                            </h5>
                            <h5 class="modal-title" v-show="editmode">
                                Update Invoice
                            </h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close"
                            >
                                <i class="fas fa-times text-white"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row p-b-10 m-l-0 m-r-0 detailbox">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <h5 class="m-b-10">Course Invoice</h5>
                                </div>
                            </div>
                            <div class="row m-b-20">
                                <div class="col-12">
                                    <table
                                        class="
                                            table table-striped table-bordered
                                        "
                                    >
                                        <thead>
                                            <tr class="text-uppercase">
                                                <th class="wf-50">SNo</th>
                                                <th>Description</th>
                                                <th class="wf-100">Cost</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <b>{{
                                                        therapy_package.name
                                                            | uppercase
                                                    }}</b
                                                    ><br />
                                                    <small>
                                                        <span
                                                            v-for="(
                                                                test, tekey
                                                            ) in therapy_package.treatments_Summary"
                                                            :key="
                                                                'ite-' + tekey
                                                            "
                                                        >
                                                            <b
                                                                >{{
                                                                    test.count
                                                                }}
                                                            </b>
                                                            {{ test.name
                                                            }}<br />
                                                        </span>
                                                        <span
                                                            v-for="(
                                                                ctest, cekey
                                                            ) in therapy_package.consultation_Summary"
                                                            :key="
                                                                'icte-' + cekey
                                                            "
                                                        >
                                                            <b
                                                                >{{
                                                                    ctest.count
                                                                }}
                                                            </b>
                                                            {{ ctest.name
                                                            }}<br />
                                                        </span>
                                                    </small>
                                                </td>
                                                <td>
                                                    <b>{{
                                                        therapy_package.cost
                                                            | formatOMR
                                                    }}</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered m-b-0">
                                        <tbody>
                                            <tr>
                                                <th
                                                    class="
                                                        text-right
                                                        text-uppercase
                                                    "
                                                >
                                                    Total
                                                </th>
                                                <td style="width: 100px">
                                                    <span>
                                                        <input
                                                            type="text"
                                                            v-model="
                                                                aform.sub_total
                                                            "
                                                            name="sub_total"
                                                            id="sub_total"
                                                            readonly="readonly"
                                                            class="form-control"
                                                            :class="{
                                                                'is-invalid':
                                                                    aform.errors.has(
                                                                        'sub_total'
                                                                    ),
                                                            }"
                                                        />
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered m-b-0">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="
                                                        text-right
                                                        text-uppercase
                                                    "
                                                >
                                                    Payable By Customer
                                                </th>
                                                <td style="width: 100px">
                                                    <input
                                                        type="text"
                                                        v-model="aform.total"
                                                        name="total"
                                                        id="total"
                                                        readonly="readonly"
                                                        class="form-control"
                                                        :class="{
                                                            'is-invalid':
                                                                aform.errors.has(
                                                                    'total'
                                                                ),
                                                        }"
                                                    />
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                    <table
                                        class="table table-bordered"
                                        v-show="aform.payment_type != 3"
                                    >
                                        <thead>
                                            <tr>
                                                <th
                                                    class="
                                                        text-right
                                                        text-uppercase
                                                    "
                                                >
                                                    Receiving Amount
                                                </th>
                                                <td style="width: 100px">
                                                    <input
                                                        type="text"
                                                        v-model="
                                                            aform.being_paid
                                                        "
                                                        name="being_paid"
                                                        id="being_paid"
                                                        class="form-control"
                                                        :class="{
                                                            'is-invalid':
                                                                aform.errors.has(
                                                                    'being_paid'
                                                                ),
                                                        }"
                                                    />
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                    <div class="row m-t-15">
                                        <div class="form-group col-md-3">
                                            <label
                                                for="payment_type"
                                                class="control-label"
                                                >Payment Type</label
                                            >
                                            <select
                                                class="form-control m-b-10"
                                                :class="{
                                                    'is-invalid':
                                                        aform.errors.has(
                                                            'payment_type'
                                                        ),
                                                }"
                                                v-model="aform.payment_type"
                                                name="payment_type"
                                                @change="setpayType"
                                            >
                                                <option value="0">
                                                    Keep Pending
                                                </option>
                                                <option value="1">
                                                    Advance Payment
                                                </option>
                                                <option value="2">
                                                    Partial Payment
                                                </option>
                                                <option value="3">
                                                    Full Payment
                                                </option>
                                            </select>
                                            <label
                                                for="payment_mode"
                                                class="control-label"
                                                >Payment Mode</label
                                            >
                                            <select
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        aform.errors.has(
                                                            'payment_mode'
                                                        ),
                                                }"
                                                v-model="aform.payment_mode"
                                                name="payment_mode"
                                            >
                                                <option value="cash">
                                                    Cash
                                                </option>
                                                <option value="credit">
                                                    Credit
                                                </option>
                                                <option value="visa">
                                                    Visa
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <span
                                                v-show="
                                                    aform.payment_mode != 'cash'
                                                "
                                            >
                                                <label
                                                    for="txn_id"
                                                    class="control-label"
                                                    >Transaction Number</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control m-b-10"
                                                    :class="{
                                                        'is-invalid':
                                                            aform.errors.has(
                                                                'txn_id'
                                                            ),
                                                    }"
                                                    v-model="aform.txn_id"
                                                    name="txn_id"
                                                />
                                            </span>
                                            <label
                                                for="remark"
                                                class="control-label"
                                                >Remark</label
                                            >
                                            <textarea
                                                class="form-control"
                                                rows="2"
                                                v-model="aform.remark"
                                                name="remark"
                                            ></textarea>
                                        </div>
                                        <div class="col-md-2"></div>

                                        <div class="form-group col-md-4">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th
                                                            colspan="2"
                                                            class="
                                                                text-center
                                                                text-uppercase
                                                            "
                                                        >
                                                            Calculate Returnable
                                                            Amount
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <label
                                                                for="received"
                                                                >Received</label
                                                            >
                                                            <input
                                                                type="text"
                                                                class="
                                                                    form-control
                                                                "
                                                                v-model="
                                                                    aform.received
                                                                "
                                                                @keyup="
                                                                    calculate
                                                                "
                                                                placeholder="enter received value"
                                                            />
                                                        </td>
                                                        <td>
                                                            <label
                                                                for="received"
                                                                >Returnable</label
                                                            >
                                                            <input
                                                                type="text"
                                                                class="
                                                                    form-control
                                                                "
                                                                v-model="
                                                                    aform.returnable
                                                                "
                                                                readonly
                                                            />
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-wide btn-danger"
                                data-dismiss="modal"
                            >
                                Cancel
                            </button>
                            <img
                                class="wf-50"
                                :src="loaderurl"
                                alt="updating"
                                v-if="loader == 1"
                            />
                            <button
                                type="submit"
                                class="btn btn-wide btn-success"
                                v-else
                            >
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div
            class="modal fullbarmodal fade"
            id="addApponitModal"
            data-backdrop="static"
            data-keyboard="false"
            aria-labelledby="addApponitModalTitle"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form
                        @submit.prevent="
                            editmode ? updateAppointment() : createAppointment()
                        "
                    >
                        <div class="modal-header">
                            <h5
                                class="modal-title"
                                id="createAppointModalTitle"
                                v-if="editmode == true"
                            >
                                Edit Appointment
                            </h5>
                            <h5
                                class="modal-title"
                                id="createAppointModalTitle"
                                v-else
                            >
                                Create Appointment
                            </h5>
                            <span class="text-right float-right"> </span>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div
                                        class="
                                            row
                                            border-bottom
                                            m-b-10
                                            p-b-5
                                            m-l-0 m-r-0
                                        "
                                    >
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label
                                                    for="appointment_type_id"
                                                    class="control-label"
                                                    >Appointment Type</label
                                                >
                                                <span
                                                    v-if="
                                                        form.appointment_type_id ==
                                                        1
                                                    "
                                                >
                                                    <label
                                                        for="treatment_id"
                                                        class="control-label"
                                                    ></label>
                                                    <input
                                                        type="text"
                                                        value="Consultaion"
                                                        class="form-control"
                                                        readonly="readonly"
                                                    />
                                                </span>
                                                <span
                                                    v-else-if="
                                                        form.appointment_type_id ==
                                                        2
                                                    "
                                                >
                                                    <input
                                                        type="text"
                                                        value="Treatment"
                                                        class="form-control"
                                                        readonly="readonly"
                                                    />
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <span
                                                v-if="
                                                    form.appointment_type_id ==
                                                    1
                                                "
                                            >
                                                <label
                                                    for="treatment_id"
                                                    class="control-label"
                                                    >Consultaion</label
                                                >
                                                <input
                                                    type="text"
                                                    v-model="form.treatment"
                                                    class="form-control"
                                                    readonly="readonly"
                                                />
                                            </span>
                                            <span
                                                v-else-if="
                                                    form.appointment_type_id ==
                                                    2
                                                "
                                            >
                                                <label
                                                    for="treatment_id"
                                                    class="control-label"
                                                    >Treatments</label
                                                >
                                                <input
                                                    type="text"
                                                    v-model="form.treatment"
                                                    class="form-control"
                                                    readonly="readonly"
                                                />
                                            </span>
                                        </div>
                                        <div class="col-3">
                                            <label class="control-label"
                                                >Default Time Required</label
                                            >
                                            <p>{{ timetaken }}</p>
                                        </div>
                                    </div>
                                    <div
                                        class="
                                            row
                                            border-bottom
                                            m-b-10
                                            p-b-5
                                            m-l-0 m-r-0
                                        "
                                    >
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label
                                                    for="date"
                                                    class="control-label"
                                                    >Appointment Date</label
                                                >
                                                <vp-date-picker
                                                    locale="en"
                                                    format="YYYY-MM-DD"
                                                    v-model="form.date"
                                                    :auto-submit="true"
                                                    :max="course.end_date"
                                                ></vp-date-picker>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <span
                                                v-if="
                                                    form.appointment_type_id ==
                                                    1
                                                "
                                            >
                                                <label
                                                    for="doctor_id"
                                                    class="control-label"
                                                    >Doctors</label
                                                >
                                                <select
                                                    v-model="form.doctor_id"
                                                    name="doctor_id"
                                                    id="doctor_id"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            form.errors.has(
                                                                'doctor_id'
                                                            ),
                                                    }"
                                                    @change="
                                                        getTimings(
                                                            1,
                                                            form.doctor_id
                                                        )
                                                    "
                                                >
                                                    <option disabled value="">
                                                        Select Doctor
                                                    </option>
                                                    <option
                                                        v-for="doctor in doctors"
                                                        :key="doctor.id"
                                                        v-bind:value="doctor.id"
                                                    >
                                                        {{
                                                            doctor.name
                                                                | capitalize
                                                        }}
                                                    </option>
                                                </select>
                                                <has-error
                                                    :form="form"
                                                    field="doctor_id"
                                                ></has-error>
                                            </span>
                                            <span
                                                v-else-if="
                                                    form.appointment_type_id ==
                                                    2
                                                "
                                            >
                                                <label
                                                    for="doctor_id"
                                                    class="control-label"
                                                    >Therapists</label
                                                >
                                                <select
                                                    v-model="form.doctor_id"
                                                    name="doctor_id"
                                                    id="doctor_id"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            form.errors.has(
                                                                'doctor_id'
                                                            ),
                                                    }"
                                                    @change="
                                                        getTimings(
                                                            2,
                                                            form.doctor_id
                                                        )
                                                    "
                                                >
                                                    <option disabled value="">
                                                        Select Therapists
                                                    </option>
                                                    <option
                                                        v-for="doctor in therapists"
                                                        :key="doctor.id"
                                                        v-bind:value="doctor.id"
                                                    >
                                                        {{
                                                            doctor.name
                                                                | capitalize
                                                        }}
                                                    </option>
                                                </select>
                                                <has-error
                                                    :form="form"
                                                    field="doctor_id"
                                                ></has-error>
                                            </span>
                                            <span v-else>
                                                <label
                                                    for="doctor_id"
                                                    class="control-label"
                                                    >Therapists/Doctors</label
                                                >
                                                <select
                                                    v-model="form.doctor_id"
                                                    name="doctor_id"
                                                    id="doctor_id"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            form.errors.has(
                                                                'doctor_id'
                                                            ),
                                                    }"
                                                >
                                                    <option disabled value="">
                                                        Select Appointment Type
                                                        first
                                                    </option>
                                                </select>
                                                <has-error
                                                    :form="form"
                                                    field="doctor_id"
                                                ></has-error>
                                            </span>
                                        </div>
                                    </div>
                                    <div
                                        class="
                                            row
                                            border-bottom
                                            m-b-10
                                            p-b-5
                                            m-l-0 m-r-0
                                        "
                                    >
                                        <div class="col-3">
                                            <label
                                                for="start_time"
                                                class="control-label"
                                                >Start Time</label
                                            >
                                            <select
                                                v-model="form.start_time"
                                                name="start_time"
                                                id="start_time"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'start_time'
                                                        ),
                                                }"
                                                @change="
                                                    getClosings(form.start_time)
                                                "
                                            >
                                                <option disabled value="">
                                                    Select Start Time
                                                </option>
                                                <option
                                                    v-for="(
                                                        timeslot, key
                                                    ) in start_times"
                                                    :key="key"
                                                    v-bind:value="key"
                                                >
                                                    {{ timeslot }}
                                                </option>
                                            </select>
                                            <has-error
                                                :form="form"
                                                field="start_time"
                                            ></has-error>
                                        </div>
                                        <div class="col-3">
                                            <label
                                                for="end_time"
                                                class="control-label"
                                                >End Time</label
                                            >
                                            <select
                                                v-model="form.end_time"
                                                name="end_time"
                                                id="end_time"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'end_time'
                                                        ),
                                                }"
                                                @change="
                                                    getRooms(form.end_time)
                                                "
                                            >
                                                <option disabled value="">
                                                    Select End Time
                                                </option>
                                                <option
                                                    v-for="(
                                                        timeslot, key
                                                    ) in end_times"
                                                    :key="key"
                                                    v-bind:value="key"
                                                >
                                                    {{ timeslot }}
                                                </option>
                                            </select>
                                            <has-error
                                                :form="form"
                                                field="end_time"
                                            ></has-error>
                                        </div>
                                        <div class="col-3" v-show="ttype == 1">
                                            <label class="control-label"
                                                >This is Dual Therapy</label
                                            >
                                            <select
                                                v-model="form.second_doctor_id"
                                                name="second_doctor_id"
                                                id="second_doctor_id"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'second_doctor_id'
                                                        ),
                                                }"
                                            >
                                                <option disabled value="">
                                                    Select Therapists
                                                </option>
                                                <option
                                                    v-for="doctor in stherapists"
                                                    :key="doctor.id"
                                                    v-bind:value="doctor.id"
                                                >
                                                    {{
                                                        doctor.name | capitalize
                                                    }}
                                                </option>
                                            </select>
                                            <has-error
                                                :form="form"
                                                field="second_doctor_id"
                                            ></has-error>
                                        </div>
                                        <div class="col-3">
                                            <span
                                                v-show="
                                                    form.appointment_type_id ==
                                                    2
                                                "
                                            >
                                                <label
                                                    for="room_id"
                                                    class="control-label"
                                                    >Rooms</label
                                                >
                                                <select
                                                    v-model="form.room_id"
                                                    name="room_id"
                                                    id="room_id"
                                                    class="form-control"
                                                    :class="{
                                                        'is-invalid':
                                                            form.errors.has(
                                                                'room_id'
                                                            ),
                                                    }"
                                                >
                                                    <option disabled value="">
                                                        Select Room
                                                    </option>
                                                    <option
                                                        v-for="(
                                                            room, key
                                                        ) in rooms"
                                                        :key="key"
                                                        v-bind:value="key"
                                                    >
                                                        {{ room }}
                                                    </option>
                                                </select>
                                                <has-error
                                                    :form="form"
                                                    field="room_id"
                                                ></has-error>
                                            </span>
                                        </div>
                                    </div>
                                    <div
                                        class="
                                            row
                                            border-bottom
                                            m-b-10
                                            p-b-5
                                            m-l-0 m-r-0
                                        "
                                    >
                                        <button
                                            class="btn btn-md btn-warning"
                                            type="button"
                                            @click="form.notify = 1"
                                            v-if="form.notify == 0"
                                        >
                                            Notify patient by SMS/Whatsapp
                                        </button>
                                        <button
                                            class="btn btn-md btn-success"
                                            type="button"
                                            @click="form.notify = 0"
                                            v-else
                                        >
                                            Patient will be notified by
                                            SMS/Whatsapp
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="doc-schdular">
                                        <div class="m-l-0 m-r-0">
                                            <ul class="patch-list">
                                                <li>
                                                    <span
                                                        class="
                                                            color-patch
                                                            bg-teal
                                                        "
                                                    ></span>
                                                    <label
                                                        class="label-control"
                                                    >
                                                        Available Slot</label
                                                    >
                                                </li>
                                                <li>
                                                    <span
                                                        class="
                                                            color-patch
                                                            bg-pink
                                                        "
                                                    ></span>
                                                    <label
                                                        class="label-control"
                                                    >
                                                        Booked Slot</label
                                                    >
                                                </li>
                                                <li>
                                                    <span
                                                        class="
                                                            color-patch
                                                            bg-opurple
                                                        "
                                                    ></span>
                                                    <label
                                                        class="label-control"
                                                    >
                                                        Blocked Slot</label
                                                    >
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="dbody">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="100px">
                                                            TimeSlots
                                                        </th>
                                                        <th>Schedule</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                        v-for="slot in timeslots"
                                                        :key="slot.id"
                                                    >
                                                        <td>{{ slot.time }}</td>
                                                        <td
                                                            :class="[
                                                                isExist(
                                                                    aval_slots,
                                                                    slot.id
                                                                )
                                                                    ? 'bg-teal'
                                                                    : isExist(
                                                                          fixedslots,
                                                                          slot.id
                                                                      )
                                                                    ? 'bg-pink'
                                                                    : 'bg-opurple',
                                                            ]"
                                                        ></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-wide btn-danger"
                                data-dismiss="modal"
                            >
                                Cancel
                            </button>
                            <img
                                class="wf-50"
                                :src="loaderurl"
                                alt="updating"
                                v-if="loader == 2"
                            />
                            <button
                                type="submit"
                                class="btn btn-wide btn-success"
                                v-else
                            >
                                <span v-if="editmode == true"> Update </span>
                                <span v-else> Create </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div
            class="modal fade"
            id="patientModal"
            data-backdrop="static"
            data-keyboard="false"
            aria-labelledby="patientModalTitle"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="updatePatient()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addDoctorModalTitle">
                                Change Patient
                            </h5>
                        </div>
                        <div class="modal-body" v-if="customer.user_profile">
                            <div class="row">
                                <div class="col-6">
                                    <label for="" class="control-label"
                                        >Current Patient</label
                                    >
                                    <hr />
                                    <ul class="popup-list">
                                        <li>
                                            <p class="alert m-0">
                                                <b>User ID</b><br />{{
                                                    customer.username
                                                }}
                                            </p>
                                        </li>
                                        <li>
                                            <p class="alert m-0">
                                                <b>Name</b><br />{{
                                                    customer.user_profile
                                                        ? customer.user_profile
                                                              .first_name
                                                        : ""
                                                }}
                                                {{
                                                    customer.user_profile
                                                        ? customer.user_profile
                                                              .last_name
                                                        : ""
                                                }}
                                            </p>
                                        </li>
                                        <li>
                                            <p class="alert m-0">
                                                <b>Contact No</b><br />{{
                                                    customer.contact_no
                                                }}
                                            </p>
                                        </li>
                                        <li>
                                            <p class="alert m-0">
                                                <b>Identity Card</b><br />{{
                                                    customer.user_document
                                                        .verification_number
                                                }}
                                                <button
                                                    class="
                                                        btn
                                                        inacn-btn
                                                        btn-success
                                                    "
                                                    v-if="
                                                        customer.user_document
                                                            .identity_verified ==
                                                        1
                                                    "
                                                >
                                                    Verified
                                                </button>
                                                <button
                                                    class="
                                                        btn
                                                        inacn-btn
                                                        btn-danger
                                                    "
                                                    v-else
                                                >
                                                    Not Verified
                                                </button>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="alert m-0">
                                                <b>Insurance</b><br />{{
                                                    customer.user_document
                                                        .policy_number
                                                }}
                                                <button
                                                    class="
                                                        btn
                                                        inacn-btn
                                                        btn-success
                                                    "
                                                    v-if="
                                                        customer.user_document
                                                            .insurance_verified ==
                                                        1
                                                    "
                                                >
                                                    Verified
                                                </button>
                                                <button
                                                    class="
                                                        btn
                                                        inacn-btn
                                                        btn-danger
                                                    "
                                                    v-else
                                                >
                                                    Not Verified
                                                </button>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="alert m-0">
                                                <b>Status</b><br />{{
                                                    customer.status.status
                                                }}
                                            </p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <label for="" class="control-label"
                                        >Choose Patient</label
                                    >
                                    <hr />
                                    <model-select
                                        :options="customers"
                                        name="user_id"
                                        id="user_id"
                                        aria-required="true"
                                        v-model="eform.user_id"
                                        placeholder="search patient"
                                    >
                                    </model-select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-wide btn-danger"
                                data-dismiss="modal"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="btn btn-wide btn-success"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <payment-list
            :showpayment="showpayment"
            :active_invoice="active_invoice"
            :payments="payments"
            :current_page="''"
        ></payment-list>
    </div>
</template>
<script>
import moment from "moment";
import PaymentList from "../Invoices/Includes/PaymentsList.vue";
export default {
    components: { PaymentList },
    data() {
        return {
            editmode: false,
            svgurl: "/svg/",
            docurl: "/files/docs/",
            course: "",
            therapy_package: "",
            invoices: "",
            catchmessage: "",
            currentYear: new Date().getFullYear(),
            activeID: "",
            activeYear: "",
            active_location: "",
            appointment: "",
            appointments: {},
            listSlots: [],
            clistSlots: [],
            customer: {},
            treatment: "",
            insurance: "",
            options: "",
            countlist: [],
            medicines: [],
            stocks: [],
            aform: new Form({
                subtotal: "",
                payment_type: 3,
                total: "",
                being_paid: "",
                payment_mode: "cash",
                txn_id: "",
                remark: "",
                returnable: "",
                received: "",
                appointments: "",
                course_code: "",
                id: "",
                user_id: "",
                invoice_number: "",
            }),
            newenabled: "",
            start_times: {},
            end_times: {},
            stherapists: [],
            consultations: {},
            treatments: {},
            timetaken: "",
            ttype: "",
            tdual: 0,
            rooms: "",
            aval_slots: {},
            doctors: "",
            timeslots: "",
            naval_slots: [],
            nblockslots: [],
            nfixedslots: [],
            therapists: "",
            fixedslots: "",
            loader: false,
            loaderurl: "/svg/loop.gif",
            form: new Form({
                id: "",
                location_id: "",
                user_id: "",
                room_id: "",
                course_id: "",
                doctor_id: "",
                appointment_type_id: "",
                course_code: "",
                treatment_id: "",
                second_doctor_id: "",
                date: "",
                start_time: "",
                treatment: "",
                end_time: "",
                visit_type_id: 1,
                ainvoice: "",
                status_id: "",
                notify: 0,
                followup_appointment: "",
                followup_verified: "",
            }),
            customers: [],
            eform: new Form({
                appointment_code: "",
                user_id: "",
                room_id: "",
                doctor_id: "",
                treatment_id: "",
                second_doctor_id: "",
                date: "",
                start_time: "",
                end_time: "",
            }),
            payment_invoice: {},
            pcalcmsg: "",
            left_therapies: {},
            new_end_date: "",
            profile: this.$store.getters.user,
            showpayment: false,
            payments: {},
            active_invoice: {},
        };
    },
    methods: {
        setAppointment(appoint) {},
        getIndex(list, id) {
            return id;
        },
        isExist(arr, cid) {
            for (let i = 0; i < arr.length; i++) {
                if (cid == parseInt(arr[i])) {
                    return true;
                }
            }
            return false;
        },
        getAge(date) {
            let today = new Date();
            let ndate = new Date(date);
            var a = moment([
                today.getFullYear(),
                today.getMonth(),
                today.getDate(),
            ]);
            var b = moment([
                ndate.getFullYear(),
                ndate.getMonth(),
                ndate.getDate(),
            ]);
            var diffDuration = moment.duration(a.diff(b));
            return "(" + diffDuration.years() + " yrs)";
        },
        viewCustomer(id) {
            $("#userModal").modal("show");
        },
        showPayment(inv, inva) {
            this.active_invoice.invoice_number = inv;
            this.active_invoice.total_amount = inva;
            this.payments = [];
            this.loader = 3;
            axios
                .post("/api/invoice-payments", { invoice_number: inv })
                .then((response) => {
                    this.payments = response.data.payments;
                    this.active_invoice.remaing_balance =
                        response.data.remaining;
                    this.loader = false;
                })
                .catch((err) => {
                    console.log(err);
                });

            this.showpayment = true;
        },
        closePayment() {
            this.showpayment = false;
        },
        setpayType() {
            let ptype = this.aform.payment_type;
            if (ptype == 3) {
                this.aform.being_paid = this.aform.total;
            } else {
                this.aform.being_paid = "";
            }
            this.aform.received = "";
            this.aform.returnable = "";
        },
        calculate() {
            let returnable =
                this.makeNumber(this.aform.received) -
                this.makeNumber(this.aform.being_paid);
            this.aform.returnable = returnable.toFixed(3);
        },
        getAllAssets() {
            axios
                .get("/api/getOnlyDoctorsList")
                .then((data) => {
                    this.doctors = data.data;
                })
                .catch();
            axios
                .get("/api/getOnlyTherapistsList")
                .then((data) => {
                    this.therapists = data.data;
                })
                .catch();
            axios
                .get("/api/getBothSlotsList")
                .then((data) => {
                    this.listSlots = data.data.starts;
                    this.clistSlots = data.data.ends;
                })
                .catch();
            axios.get("/api/get-time-slots").then(({ data }) => {
                this.timeslots = data;
            });
        },
        showCourse() {
            this.$Progress.start();
            axios
                .get("/api/courses/get-package-detail/" + this.activeID)
                .then(({ data }) => {
                    this.course = data.course;
                    this.activeYear =
                        "20" + data.course.course_code.substring(1, 3);
                    this.invoices = data.invoices;
                    this.customer = data.customer;
                    this.therapy_package = data.therapy_package;
                    this.appointments = data.appointments;
                    this.left_therapies = data.left_therapies;
                    this.$Progress.finish();
                });
        },
        setInvoice() {
            this.aform.sub_total = this.therapy_package.cost;
            this.aform.total = this.therapy_package.cost;
            this.aform.being_paid = this.therapy_package.cost;
            this.aform.user_id = this.course.user_id;
            this.aform.course_code = this.course.course_code;
            this.aform.appointments = this.course.appointments;
            $("#setInvocieModal").modal("show");
        },
        makeNumber(val) {
            if (isNaN(val)) {
                return 0;
            } else {
                return parseFloat(val);
            }
        },
        createInvoice() {
            this.loader = 1;
            this.aform
                .post("/api/invoices/create-course-invoice")
                .then((data) => {
                    this.loader = false;
                    $("#setInvocieModal").modal("hide");
                    Fire.$emit("AfterCreate");
                    swal.fire({
                        title: "Invoice has been created successfully",
                        type: "success",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Print Invoice",
                        cancelButtonText: "Stay On The Page",
                    })
                        .then((result) => {
                            if (result.value) {
                                //this.$router.push('/invoices/print-coursep-invoice/'+data.invoice);
                                let route = this.$router.resolve({
                                    path:
                                        "/invoices/print-coursep-uinvoice/" +
                                        data.invoice,
                                });
                                window.open(route.href, "_blank");
                            }
                        })
                        .catch(() => {
                            swal.showValidationMessage(
                                `Request failed: ${error}`
                            );
                        });
                })
                .catch(() => {
                    this.loader = false;
                    swal.showValidationMessage(`Request failed: ${error}`);
                });
        },
        isNumeric(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        },

        removeADiscount() {
            this.aform.discount_id = "";
            this.aform.offered_value = 0;
            this.aform.offered = 0;
            this.aform.offered_reason = "";
            this.aform.paying_now = this.aform.total;
            this.aform.payment_type = 3;
            this.aform.cash_amount = "";
            this.aform.visa_amount = "";
            this.reTotal();
        },
        removeBar(barkey) {
            this.acountlist.splice(barkey, 1);
            this.aform.sub_total =
                this.aform.sub_total - this.aform.apt__cost[barkey];
            this.aform.apt__reason.splice(barkey, 1);
            this.aform.apt__apcode.splice(barkey, 1);
            this.aform.apt__datetime.splice(barkey, 1);
            this.aform.apt__cost.splice(barkey, 1);
        },
        reTotal() {
            this.aform.returnable = "";
            this.aform.received = "";
            if (this.aform.insured == 1) {
                //  this.addInsurance();
                let insured_deduction =
                    this.makeNumber(this.aform.sub_total) -
                    this.makeNumber(this.aform.total);
                this.aform.insured_deduction =
                    this.makeNumber(insured_deduction) >= 0
                        ? this.makeNumber(insured_deduction).toFixed(3)
                        : 0;
            } else if (this.aform.offered == 1) {
                this.aform.insured = "";
                //    this.addOffers();
                let total =
                    this.makeNumber(this.aform.sub_total) -
                    this.makeNumber(this.aform.offered_value);
                this.aform.total =
                    this.makeNumber(total) >= 0
                        ? this.makeNumber(total).toFixed(3)
                        : 0;
            } else {
                this.aform.insured_deduction = 0;
                this.aform.total = this.makeNumber(
                    this.aform.sub_total
                ).toFixed(3);
            }
            if (this.aform.payment_type == 2) {
                if (this.aform.paying_now > this.aform.total) {
                    this.aform.paying_now = this.aform.total;
                }
            } else {
                this.aform.paying_now = this.aform.total;
            }
        },
        showType() {
            let treat = this.form.treatment_id;
            this.form.second_doctor_id = "";
            axios
                .get("/api/treatments/" + treat)
                .then((data) => {
                    this.timetaken = data.data.timing + " mins";
                    if (data.data.is_it_dual >= 1) {
                        this.ttype = 1;
                        this.tdual = data.data.is_it_dual;
                    } else {
                        this.ttype = 0;
                        this.tdual = 0;
                    }
                })
                .catch();
        },
        getTimings(atype, did) {
            axios
                .get(
                    "/api/appointments/get-doctor-appointment-timings?q=" +
                        this.form.date +
                        "&at=" +
                        atype +
                        "&lid=" +
                        this.form.location_id +
                        "&did=" +
                        did
                )
                .then(({ data }) => {
                    this.blockslots = data["blocked"];
                    this.fixedslots = data["fixed"];
                    this.start_times = data["start_slots"];
                    this.aval_slots = data["aval_slots"];
                });
        },
        getClosings(st) {
            axios
                .get(
                    "/api/appointments/get-end-timings?q=" +
                        this.form.date +
                        "&at=" +
                        this.form.appointment_type_id +
                        "&lid=" +
                        this.form.location_id +
                        "&did=" +
                        this.form.doctor_id +
                        "&st=" +
                        st
                )
                .then(({ data }) => {
                    this.end_times = data;
                });
        },
        getRooms(et) {
            axios
                .get(
                    "/api/appointments/get-rooms?q=" +
                        this.form.date +
                        "&lid=" +
                        this.form.location_id +
                        "&st=" +
                        this.form.start_time +
                        "&et=" +
                        et
                )
                .then(({ data }) => {
                    this.rooms = data;
                });
            if (this.ttype == 1) {
                axios
                    .get(
                        "/api/appointments/get-second-therapist?q=" +
                            this.form.date +
                            "&lid=" +
                            this.form.location_id +
                            "&did=" +
                            this.form.doctor_id +
                            "&st=" +
                            this.form.start_time +
                            "&et=" +
                            et +
                            "&dtype=" +
                            this.tdual
                    )
                    .then(({ data }) => {
                        this.stherapists = data;
                    });
            }
        },
        makeAppointment(thert) {
            this.catchmessage = "";
            this.form.reset();
            this.blockslots = [];
            this.fixedslots = [];
            this.start_times = [];
            this.form.location_id = this.course.location_id;
            this.form.course_id = this.course.course_code;
            this.form.user_id = this.course.user_id;
            this.form.treatment_id = thert.id;
            this.form.treatment = thert.name;
            this.form.appointment_type_id = thert.appointment_type_id;
            this.form.ainvoice = this.course.invoice;
            if (this.course.status_id == 9) {
                this.form.status_id = 9;
            } else {
                this.form.status_id = 4;
            }
            //console.log(this.form.date);
            this.showType();
            $("#addApponitModal").modal("show");
        },
        createAppointment() {
            this.loader = 2;
            this.form
                .post("/api/courses/make-schcourse-appointment")
                .then(() => {
                    this.loader = false;
                    this.$Progress.start();
                    Fire.$emit("AfterCreate");
                    $("#addApponitModal").modal("hide");
                    toast.fire({
                        type: "success",
                        title: "Appointment created successfully.",
                    });
                    this.$Progress.finish();
                })
                .catch((response) => {
                    this.loader = false;
                    // this.catchmessage = response.message;
                    //this.flash(response.message, 'error');
                });
        },
        cancelAppointment(id) {
            swal.fire({
                title: "Are you sure?",
                text: "Please enter the reason before cancelling the appointment.",
                footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                type: "question",
                input: "text",
                inputAttributes: { autocapitalize: "off" },
                showCancelButton: true,
                confirmButtonColor: "#C70039",
                cancelButtonColor: "#0DC957",
                confirmButtonText: "Yes, Cancel it!",
                cancelButtonText: "Not Now",
                preConfirm: (reason) => {
                    return axios
                        .post("/api/cancel-appointment", {
                            appointment_code: id,
                            reason: reason,
                        })
                        .then((response) => {
                            Fire.$emit("AfterCreate");
                            swal.fire(
                                "Cancelled!",
                                "Appointment has been cancelled successfully.",
                                "success"
                            );
                        })
                        .catch((error) => {
                            swal.showValidationMessage(`${error}`);
                        });
                },
            });
        },
        onDateSelect() {
            let apid = this.form.appointment_type_id;
            let did = this.form.doctor_id;
            let st = this.form.start_time;
            let et = this.form.end_time;
            if (this.form.appointment_code) {
                this.getTimings(apid, did);
                this.getClosings(st);
                this.getRooms(et);
            }
        },
        patientBox(apid) {
            this.eform.appointment_code = apid.appointment_code;
            axios
                .get("/api/getCustomerSelectList")
                .then((data) => {
                    this.customers = data.data;
                })
                .catch();
            $("#patientModal").modal("show");
        },
        doctorBox(apid) {
            let pi = this.form;
            this.editmode = true;
            //this.treatments = this.therapy_package.treatmentlists;

            pi.appointment_code = apid.appointment_code;
            axios
                .get("/api/appointments/form-view/" + apid.appointment_code)
                .then((data) => {
                    pi.fill(data.data);
                    axios
                        .get("/api/treatments/" + data.data.treatment_id)
                        .then((data2) => {
                            this.timetaken = data2.data.timing + " mins";
                            pi.treatment = data2.data.treatment;
                            if (data2.data.is_it_dual == 1) {
                                this.ttype = 1;
                                axios
                                    .get(
                                        "/api/appointments/get-second-therapist?q=" +
                                            data.data.date +
                                            "&lid=" +
                                            data.data.location_id +
                                            "&st=" +
                                            data.data.start_timeslot +
                                            "&et=" +
                                            data.data.end_timeslot +
                                            "&apid=" +
                                            data.data.appointment_code +
                                            "&did=" +
                                            this.form.doctor_id +
                                            "&dtype=" +
                                            this.tdual
                                    )
                                    .then(({ data }) => {
                                        this.stherapists = data;
                                    });
                            } else {
                                this.ttype = 0;
                            }
                        })
                        .catch();
                    axios
                        .get(
                            "/api/appointments/get-doctor-appointment-timings?q=" +
                                data.data.date +
                                "&at=" +
                                data.data.appointment_type_id +
                                "&lid=" +
                                data.data.location_id +
                                "&did=" +
                                data.data.doctor_id +
                                "&apid=" +
                                data.data.appointment_code
                        )
                        .then((data3) => {
                            this.nblockslots = data3.data["blocked"];
                            this.nfixedslots = data3.data["fixed"];
                            this.start_times = data3.data["start_slots"];
                            this.naval_slots = data3.data["aval_slots"];
                        });
                    axios
                        .get(
                            "/api/appointments/get-end-timings?q=" +
                                data.data.date +
                                "&at=" +
                                data.data.appointment_type_id +
                                "&lid=" +
                                data.data.location_id +
                                "&did=" +
                                data.data.doctor_id +
                                "&st=" +
                                data.data.start_timeslot +
                                "&apid=" +
                                data.data.appointment_code
                        )
                        .then((data4) => {
                            this.end_times = data4.data;
                        });
                    axios
                        .get(
                            "/api/appointments/get-rooms?q=" +
                                data.data.date +
                                "&lid=" +
                                data.data.location_id +
                                "&st=" +
                                data.data.start_timeslot +
                                "&et=" +
                                data.data.end_timeslot +
                                "&apid=" +
                                data.data.appointment_code
                        )
                        .then((data5) => {
                            this.rooms = data5.data;
                        });
                    pi.start_time = data.data.start_timeslot;
                    pi.end_time = data.data.end_timeslot;
                })
                .catch();
            $("#addApponitModal").modal("show");
        },
        updatePatient() {
            this.eform
                .post("/api/appointments/change-patient")
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit("AfterCreate");
                    $("#patientModal").modal("hide");
                    toast.fire({
                        type: "success",
                        title: "Patient has been updated successfully.",
                    });
                    this.$Progress.finish();
                })
                .catch((response) => {
                    // this.catchmessage = response.message;
                    //this.flash(response.message, 'error');
                });
        },
        updateAppointment() {
            this.form
                .post("/api/appointments/update-appointment")
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit("AfterCreate");
                    $("#addApponitModal").modal("hide");
                    this.editmode = false;
                    toast.fire({
                        type: "success",
                        title: "Appointment has been updated successfully.",
                    });
                    this.form.reset();
                    this.$Progress.finish();
                })
                .catch((response) => {
                    // this.catchmessage = response.message;
                    //this.flash(response.message, 'error');
                });
        },
        editEndDate() {
            if (this.new_end_date) {
                this.loader = 5;
                axios
                    .post("/api/update-end-date", {
                        end_date: this.new_end_date,
                        id: this.activeID,
                    })
                    .then(() => {
                        Fire.$emit("AfterCreate");
                        this.new_end_date = "";
                        toast.fire({
                            type: "success",
                            title: "Date has been updated successfully.",
                        });
                        this.loader = false;
                    })
                    .catch((response) => {
                        console.log(response);
                    });
            }
        },
        showInvoices() {},
    },
    beforeMount() {
        let activeId = this.$route.path.split("/");
        this.activeID = activeId[4];
        this.showCourse();
    },
    created() {
        this.getAllAssets();
        Fire.$on("AfterCreate", () => {
            this.showCourse();
        });
    },
};
</script>
