import { Backdrop, Button, Fade, Grid, makeStyles, Modal, Typography } from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import { ValidatorForm } from 'react-material-ui-form-validator';
import { useDispatch } from 'react-redux';
import { addCategory, updateCategory } from '../../actions/categories';
import Input from '../../components/UI/Input';

const useStyles = makeStyles((theme) => ({
    modal: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
    },
    paper: {
        backgroundColor: theme.palette.background.paper,
        border: '2px solid #000',
        boxShadow: theme.shadows[5],
        padding: theme.spacing(2, 4, 3),
        width: '500px'
    },
    form: {
        width: '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(3),
    },
    submit: {
        margin: theme.spacing(3, 0, 2),
    },
}));

const AddCategory = ({handleClose, open, category}) => {
    const classes = useStyles();
    const [categoryData, setCategoryData] = useState({name: ''});
    const dispatch = useDispatch();
    var formData = new FormData();

    useEffect(() => {
        if(category)
        {
            setCategoryData({ ...category, name: category.name});
        }
        else
        {
            setCategoryData({name: ''});
        }
    }, [category])

    const submitCategory = async (e) => {
        e.preventDefault();
        if(category)
        {
            formData.append('category_id', category.long_id);
            formData.append('name', categoryData.name);
            dispatch(updateCategory(formData));
        }
        else {
            formData.append('name', categoryData.name);
            dispatch(addCategory(formData));
        }
        handleClose();
        setCategoryData({name: ''});
    }

    return (
        <Modal
            aria-labelledby="transition-modal-title"
            aria-describedby="transition-modal-description"
            className={classes.modal}
            open={open}
            onClose={handleClose}
            closeAfterTransition
            BackdropComponent={Backdrop}
            BackdropProps={{
                timeout: 500,
            }}
        >
            <Fade in={open}>
                <div className={classes.paper}>
                    <Typography component="h1" variant="h5">
                        {category ? 'Edit' : 'Add'}  category
                    </Typography>
                    <ValidatorForm className={classes.form} noValidate onSubmit={submitCategory}>
                        <Grid container spacing={2}>
                            <Grid item xs={12}>
                                <Input
                                    name="name"
                                    variant="outlined"
                                    required
                                    fullWidth
                                    id="categoryName"
                                    label="Category Name"
                                    placeholder="Category Name"
                                    autoFocus
                                    value={categoryData.name}
                                    handleChange={(e) => setCategoryData({...categoryData, name: e.target.value})}
                                />
                            </Grid>   
                        </Grid>
                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            color="primary"
                            className={classes.submit}
                        >
                            Submit
                        </Button>
                    </ValidatorForm>
                </div>
            </Fade>
        </Modal>
    )
}
export default AddCategory;