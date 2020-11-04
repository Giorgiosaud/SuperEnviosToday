import { format, parseISO } from 'date-fns';

export const datetime = (time) => format(parseISO(time), 'dd-mm-yyyy HH:mm');
export default datetime;
