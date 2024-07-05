import { toast } from 'vue3-toastify';

export const successNotify = (msg, options) => {
  toast.success(msg, {
    position: toast.POSITION.BOTTOM_CENTER,
    ...options
  })
};
export const errorNotify = (msg, options) => {
  toast.error(msg, {
    position: toast.POSITION.BOTTOM_CENTER,
    ...options
  })
};
export const infoNotify = (msg, options) => {
  toast.info(msg, {
    position: toast.POSITION.BOTTOM_CENTER,
    ...options
  })
};
