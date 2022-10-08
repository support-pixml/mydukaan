import React from 'react';
import { Button, Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle } from '@material-ui/core';

const AlertDialogue = ({open, handleClose, title, message, handleAction}) => {

    return (
        <Dialog
            open={open}
            keepMounted
            onClose={handleClose}
            aria-labelledby="alert-dialog-slide-title"
            aria-describedby="alert-dialog-slide-description"
        >
            <DialogTitle id="alert-dialog-slide-title">{title}</DialogTitle>
            <DialogContent>
            <DialogContentText id="alert-dialog-slide-description">
                {message}
            </DialogContentText>
            </DialogContent>
            <DialogActions>
            <Button onClick={handleClose} color="primary">
                Disagree
            </Button>
            <Button onClick={handleAction} color="primary">
                Agree
            </Button>
            </DialogActions>
        </Dialog>
    );
}

export default AlertDialogue;