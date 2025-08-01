import type * as express from "express";
import ReactDOMServer from "react-dom/server";
import {dehydrate, QueryClient} from "@tanstack/react-query";

import {router} from "./router";
import {App} from "./App";
import {setAuthToken} from "./utilites/apiClient.ts";
import {createStaticHandler, createStaticRouter, StaticRouterProvider} from "react-router";
import {dynamicActivateLocale} from "./locales.ts";
import {setSsrQueryClient} from "./utilites/ssrQueryClient.ts";

const getLocale = (req: express.Request): string => {
    if (req.cookies.locale) {
        return req.cookies.locale;
    }

    const acceptLanguage = req.headers['accept-language'];
    return acceptLanguage ? acceptLanguage.split(',')[0].split('-')[0] : 'en';
}

export async function render(params: {
    req: express.Request;
    res: express.Response;
}) {
    setAuthToken(params.req.cookies.token);

    // Create a fresh query client for each request
    const queryClient = new QueryClient({
        defaultOptions: {
            queries: {
                staleTime: 60 * 1000, // 60 seconds - prevents immediate refetch on client
                refetchOnWindowFocus: false,
                networkMode: "always",
            },
            mutations: {
                networkMode: 'always',
            }
        },
    });

    setSsrQueryClient(queryClient);

    const helmetContext = {};

    const {query, dataRoutes} = createStaticHandler(router);
    const remixRequest = createFetchRequest(params.req, params.res);
    const context = await query(remixRequest);

    if (context instanceof Response) {
        throw context;
    }

    await dynamicActivateLocale(getLocale(params.req));

    const routerWithContext = createStaticRouter(dataRoutes, context);
    
    const appHtml = ReactDOMServer.renderToString(
        <App
            queryClient={queryClient}
            helmetContext={helmetContext}
            locale={getLocale(params.req)}
        >
            <StaticRouterProvider
                router={routerWithContext}
                context={context}
            />
        </App>
    );

    const dehydratedState = dehydrate(queryClient);

    // Clean up the SSR query client
    setSsrQueryClient(null);

    return {
        appHtml: appHtml,
        dehydratedState,
        helmetContext,
    };
}

export function createFetchRequest(
    req: express.Request,
    res: express.Response
): Request {
    const origin = `${req.protocol}://${req.get("host")}`;
    const url = new URL(req.originalUrl || req.url, origin);
    const controller = new AbortController();
    res.on("close", () => controller.abort());

    const headers = new Headers();

    for (const [key, values] of Object.entries(req.headers)) {
        if (values) {
            if (Array.isArray(values)) {
                for (const value of values) {
                    headers.append(key, value);
                }
            } else {
                headers.set(key, values);
            }
        }
    }

    const init: RequestInit = {
        method: req.method,
        headers,
        signal: controller.signal,
    };

    if (req.method !== "GET" && req.method !== "HEAD") {
        init.body = req.body;
    }

    return new Request(url.href, init);
}
