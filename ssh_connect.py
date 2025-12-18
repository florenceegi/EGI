import pty
import os
import sys
import time

def read_until(fd, marker):
    buf = b""
    while True:
        try:
            chunk = os.read(fd, 1)
            if not chunk:
                break
            buf += chunk
            if marker in buf:
                return buf
        except OSError:
            break
    return buf

def main():
    passphrase = "Hillbert9#"
    cmd = sys.argv[1]
    
    pid, fd = pty.fork()
    
    if pid == 0:
        # Child
        os.execvp("ssh", ["ssh", "-o", "StrictHostKeyChecking=no", "forge@157.245.20.197", cmd])
    else:
        # Parent
        output = read_until(fd, b"passphrase")
        if b"passphrase" in output:
            time.sleep(1)
            os.write(fd, passphrase.encode() + b"\n")
            
        # Read the rest of the output
        while True:
            try:
                chunk = os.read(fd, 1024)
                if not chunk:
                    break
                sys.stdout.buffer.write(chunk)
                sys.stdout.buffer.flush()
            except OSError:
                break
                
        os.waitpid(pid, 0)

if __name__ == "__main__":
    main()
