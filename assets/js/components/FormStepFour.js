import React from 'react';
import ReactDOM from 'react-dom';
import {withFormik, Form, ErrorMessage, Field, getIn} from "formik";
import axios from 'axios';
import qs from 'qs';
import Select from 'react-select';
import * as Yup from 'yup';

const options = [
    { value: 'Food', label: 'Food' },
    { value: 'Being Fabulous', label: 'Being Fabulous' },
    { value: 'Ken Wheeler', label: 'Ken Wheeler' },
    { value: 'ReasonML', label: 'ReasonML' },
    { value: 'Unicorns', label: 'Unicorns' },
    { value: 'Kittens', label: 'Kittens' },
];

const FormStepFour = ({
    values,
    errors,
    handleChange,
    handleSubmit,
    setFieldValue,
    isSubmitting,
    setFieldTouched,
 }) => (
    <div className="formik-container">
        <div className="form-progress-bar"><div className="form-progress form-progress-w80"><b>80% Completed</b></div></div>
        <Form onSubmit={handleSubmit} className="d-flex flex-column form-step-1">
            <h4 className="text-center">Please provide the shipping address to where you would like your bottle sent.</h4>
            <div className="form-group">
                <ErrorMessage
                    component="div"
                    name="customer.shippingInfo.name"
                    className="text-uppercase text-danger small position-absolute"
                />
                <Field
                    name="customer.shippingInfo.name"
                    placeholder="Name*"
                    type="text"
                    className="form-control formik-input mt-3"
                />
            </div>
            <div className="form-group">
                <ErrorMessage
                    component="div"
                    name="customer.shippingInfo.street"
                    className="text-uppercase text-danger small position-absolute"
                />
                <Field
                    name="customer.shippingInfo.street"
                    placeholder="Street Address*"
                    type="text"
                    className="form-control formik-input mt-3"
                />
            </div>

            <div className="form-group">
                <ErrorMessage
                    component="div"
                    name="customer.shippingInfo.apt"
                    className="text-uppercase text-danger small position-absolute"
                />
                <Field
                    name="customer.shippingInfo.apt"
                    placeholder="Apt/Suite"
                    type="text"
                    className={`form-control formik-input mt-3 ${getIn(errors, "customer.shippingInfo.apt") ? 'border-danger': ''}`}
                />
            </div>
            <div className="form-group">
                <ErrorMessage
                    component="div"
                    name="customer.shippingInfo.city"
                    className="text-uppercase text-danger small position-absolute"
                />
                <Field
                    name="customer.shippingInfo.city"
                    placeholder="City*"
                    type="text"
                    className="form-control formik-input mt-3"
                />
            </div>
            <div className="form-group">
                <ErrorMessage
                    component="div"
                    name="customer.shippingInfo.state"
                    className="text-uppercase text-danger small position-absolute"
                />
                <Field
                    name="customer.shippingInfo.state"
                    placeholder="State*"
                    type="text"
                    className="form-control formik-input mt-3"
                />
            </div>
            <div className="form-group">
                <ErrorMessage
                    component="div"
                    name="customer.shippingInfo.zipCode"
                    className="text-uppercase text-danger small position-absolute"
                />
                <Field
                    name="customer.shippingInfo.zipCode"
                    placeholder="ZipCode*"
                    type="text"
                    className="form-control formik-input mt-3"
                />
            </div>
            {errors&&(<p style={{ color: 'red' }}>{errors.general}</p>)}
            <p className="text-center m-0"><small>*Required fields</small></p>
            <button type="submit" className="btn btn-success text-uppercase font-weight-bold">Get my free bottle</button>
            <p className="text-justify mt-2 info">*Limit one free product per household. Only valid for full priced purchases. Proof of purchase from an authorized retailer may also be required. No additional purchase is necessary. Please allow 1-2 weeks for delivery. Our offer is not in any way dependent on feedback that you provide, whether that be positive or negative. Offer only valid in the United States of America; void where prohibited. Offer valid while supplies last and subject to change or cancellation at any time.</p>
        </Form>
    </div>
)

const FormikStepFour = withFormik({
    mapPropsToValues({customer, handleStep }){
        return {
            customer,
            handleStep
        }
    },
    // validateOnChange: false,
    validationSchema: Yup.object().shape({
        customer:Yup.object().shape({
            shippingInfo: Yup.object().shape({
                name:Yup.string().required('Required').nullable(),
                street:Yup.string().required('Required').nullable(),
                apt: Yup.string().nullable(),
                city: Yup.string().required('Required').nullable(),
                state: Yup.string().required('Required').nullable(),
                zipCode: Yup.string().required('Required').nullable()
            })
        })
    }),
    handleSubmit(values, actions){
        console.log(values)
        // const {email, first_name, last_name, order_id, product} = values;
        //
        const data = {order_id:values.customer.orderId, shippingInfo: values.customer.shippingInfo};
        console.log(data)
        axios.post('/step4', qs.stringify(data),{
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
        })
            .then(function (response) {
                const customer = {};
                customer.step = 1;
                values.handleStep(customer);
            })
            .catch(function (error) {
                console.log(error.response);
                actions.setFieldError(error.response.data.field, error.response.data.message);
            });
    }

})(FormStepFour);

export default FormikStepFour;