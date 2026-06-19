export const useFlash = () => {
  const flash = useState<{ message: string; type: 'success' | 'error' } | null>(
    'flash',
    () => null,
  );

  const setFlash = (message: string, type: 'success' | 'error' = 'success') => {
    flash.value = { message, type };
    setTimeout(() => {
      flash.value = null;
    }, 3000);
  };

  return { flash, setFlash };
};
