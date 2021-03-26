import { Backdrop, Button, Fade, Grid, makeStyles, Modal, Typography } from '@material-ui/core';
import React, { useState } from 'react';
import { useDispatch } from 'react-redux';
import { addCategory } from '../../actions/categories';
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

const AddCategory = ({handleClose, open}) => {
    const classes = useStyles();
    const [categoryData, setCategoryData] = useState({name: '', image: null});
    const dispatch = useDispatch();
    var formData = new FormData();

    const submitCategory = async (e) => {
        e.preventDefault();
        formData.append('name', categoryData.name);
        formData.append('image', categoryData.image);
        dispatch(addCategory(formData));
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
                        Add category
                    </Typography>
                    <form className={classes.form} noValidate onSubmit={submitCategory}>
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
                                    handleChange={(e) => setCategoryData({...categoryData, name: e.target.value})}
                                />
                            </Grid>   
                        </Grid>
                        <Grid container spacing={3}>
                            <Grid item xs={12}>
                                <input type="file" onChange={(e) => setCategoryData({...categoryData, image: e.target.files[0]})} />
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
                    </form>
                </div>
            </Fade>
        </Modal>
    )
}
export default AddCategory;