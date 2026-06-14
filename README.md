# Framework Factory

> Framework Factory is a PHP application starter package. The aim is to make building PHP 8 powered applications a
> breeze,
> by giving a _Laravel`esque_ construct for the beginning stages of application development. Provided is an application
> entrypoint interface and the logic needed to get your next project going with minimal boilerplate overhead.
>
> - You can see a basic demonstration implementation by visiting the demo
    repository [here](https://github.com/FrameworkFactoryPHP/demo).
> - All documentation is provided in the [separate `docs` repository](https://github.com/FrameworkFactoryPHP/docs).

## Features
- **Application entrypoint:** The application entrypoint is located at `FrameworkFactory\Application`, and assists in
  managing your application by providing an elegant Boostrap API.
- **PSR-11 IoC Container:** The IoC container is the heart of Framework Factory. It is a robust container
  that includes many features for managing dependencies within your application, as well as giving you access to them.
    - **Container Features**:
        - Container lifecycle hooks
        - Context binding support
        - Provider caching
        - Facade support
        - Lazy loading
- **Fully tested codebase:** The codebase is fully tested using the [Pest PHP](https://pestphp.com/) testing framework.
  You can see all tests by going [here](https://github.com/FrameworkFactoryPHP/core/tree/main/tests).

#### License

BSD 3-Clause License

Copyright (c) 2026, Framework-Factory

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice,
   this list of conditions and the following disclaimer in the documentation
   and/or other materials provided with the distribution.

3. Neither the name of the copyright holder nor the names of its
   contributors may be used to endorse or promote products derived from
   this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.