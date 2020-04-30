import React from 'react';
import ReactDOM from 'react-dom';
import {withFormik, Form} from "formik";
import axios from 'axios';
import qs from 'qs';
import Select from 'react-select';
import * as Yup from 'yup';

const FormStepOne = ({
    values,
    products,
    errors,
    handleChange,
    handleSubmit,
    setFieldValue,
    isSubmitting,
    setFieldTouched,
 }) => (
    <div className="formik-container">
        <Form onSubmit={handleSubmit} className="d-flex flex-column form-step-1">
            <h4 className="text-center smaller">Share your experience with us and receive a free bottle!*</h4>
            <p className="text-center pb-1"><small>The process is quick and easy! To begin, please complete the fields below</small></p>
            {errors.first_name&&(<small className="text-uppercase text-danger">{errors.first_name}</small>)}
            <div className="form-group">
                <input type="text" className="form-control formik-input" placeholder = "First Name*" name="first_name" value={values.first_name} onChange={handleChange}/>
            </div>
            {errors.last_name&&(<small className="text-uppercase text-danger">{errors.last_name}</small>)}
            <div className="form-group">
                <input type="text" className="form-control formik-input" placeholder = "Last Name*" name="last_name" value={values.last_name} onChange={handleChange}/>
            </div>
            {errors.email&&(<small className="text-uppercasetext-danger">Email error</small>)}
            <div className="form-group">
                <input type="email" className="form-control formik-input" placeholder = "Email Address*" name="email" value={values.email} onChange={handleChange}/>
            </div>
            <div className="form-group">
                <Select
                    id="color"
                    options={products}
                    placeholder = "Select Product*"
                    multi={true}
                    onChange={(value) => setFieldValue('product', value)}
                    onBlur={()=> setFieldTouched('product', true)}
                    value={values.product}
                    className="formik-input"
                />
            </div>
            {values.product&&(
                <React.Fragment>
                    {errors.order_id&&(<small className="text-uppercase text-danger">{errors.order_id}</small>)}
                <div className="form-group">
                    <input type="text" className="form-control formik-input" placeholder = "Order Id*" name="order_id" value={values.order_id} onChange={handleChange}/>
                </div>
                </React.Fragment>
            )}
            {errors&&(<p className="text-center text-uppercase text-danger m-0">{errors.general}</p>)}
            <p className="text-center m-0"><small>*Required fields</small></p>
            <button type="submit" className="btn btn-success text-uppercase font-weight-bold">Get my free bottle</button>
            <p className="text-center mt-2 info text-uppercase">No shipping charges, no hidden fees, no credit card required!</p>
        </Form>
    </div>
)

const FormikStepOne = withFormik({
    mapPropsToValues({first_name, last_name, email, product, order_id, handleStep }){
        return {
            first_name: first_name || '',
            last_name: last_name || '',
            email: email || '',
            product: product || '',
            order_id: order_id || '',
            handleStep: handleStep
        }
    },
    // validateOnChange: false,
    validationSchema: Yup.object().shape({
        first_name: Yup.string().required(),
        last_name: Yup.string().required(),
        email: Yup.string().email().required(),
        order_id: Yup
            .string()
        .matches(/^\d{3}-\d{7}-\d{7}$/, 'Format 100-1234567-1234567').required(),
    }),
    handleSubmit(values, actions){
        console.log(values)
        const {email, first_name, last_name, order_id, product} = values;

        const data = {email, first_name, last_name, order_id, product};
        axios.post('/step1', qs.stringify(data),{
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
        })
            .then(function (response) {
                const customer = JSON.parse(response.data);
                customer.step = 1;
                values.handleStep(customer);
            })
            .catch(function (error) {
                console.log(error.response);
                actions.setFieldError(error.response.data.field, error.response.data.message);
            })
            .catch(error =>{
                actions.setFieldError('general', 'Server Error try again later')
            });
    }

})(FormStepOne);

export default FormikStepOne;